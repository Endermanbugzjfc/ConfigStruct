<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\ParseError\TypeMismatchError;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use Endermanbugzjfc\ConfigStruct\StructureError;
use Endermanbugzjfc\ConfigStruct\Utils\ReflectionUtils;
use Endermanbugzjfc\ConfigStruct\Utils\StructureErrorThrowerTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use TypeError;
use function array_map;
use function array_merge;
use function array_unique;
use function class_exists;
use function get_debug_type;

/**
 * @template T of object
 */
final class ObjectContext
{
    use StructureErrorThrowerTrait;

    /**
     * @param ReflectionClass<T> $reflection
     * @param BasePropertyContext[] $propertyContexts Key = property name.
     * @param mixed[] $unhandledElements Raw value of elements in the input which do not have the corresponding property.
     * @param ReflectionProperty[] $missingElements Key = property name.
     */
    public function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertyContexts,
        protected array           $unhandledElements,
        protected array           $missingElements
    ) {
    }

    /**
     * @return ReflectionClass<T>
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return mixed[] Raw value of elements in the input which do not have the corresponding property. Please notice that some properties might also have their unhandled elements, see {@link BasePropertyContext::getUnhandledElements()}.
     */
    public function getUnhandledElements() : array
    {
        return $this->unhandledElements;
    }

    /**
     * @return ReflectionProperty[] Key = property name.
     */
    public function getMissingElements() : array
    {
        return $this->missingElements;
    }

    /**
     * @return BasePropertyContext[] Key = property name.
     */
    public function getPropertyContexts() : array
    {
        return $this->propertyContexts;
    }

    /**
     * Copy output data to the given object.
     * @param object $object This object will be modified.
     * @param string $rootHeaderLabel See {@link ParseErrorsWrapper::getRootHeaderLabel()}.
     * @throws ParseErrorsWrapper
     */
    public function copyToObject(
        object $object,
        string $rootHeaderLabel
    ) : void {
        $propertyContexts = $this->getPropertyContexts();
        $tree = $this->getErrorsTree();
        foreach ($propertyContexts as $propertyContext) {
            if ($propertyContext->omitCopyToObject()) {
                continue;
            }

            $reflection = $propertyContext->getDetails()->getReflection();
            $value = $propertyContext->getValue();
            try {
                $reflection->setValue(
                    $object,
                    $value
                );
            } catch (TypeError $err) {
                $expectedTypes = array_unique(
                    array_map(
                        static fn(ReflectionNamedType $type) : string => (
                            class_exists($raw = $type->getName())
                            or
                            $raw === "self"
                        )
                            ? "array"
                            : $raw,
                            ReflectionUtils::getPropertyTypes($reflection)
                    )
                );
                $types = $reflection->getType();
                if ($types?->allowsNull() ?? true) {
                    $expectedTypes[] = "null";
                }
                $expectedTypes = array_unique(
                    $expectedTypes // There might be multiple "null"s.
                );

                $treeKey = $propertyContext->getErrorsTreeKey();
                $tree[$treeKey][] = new TypeMismatchError(
                    $err,
                    $expectedTypes,
                    get_debug_type($value)
                );
            }
        }

        if ($tree !== []) {
            throw new ParseErrorsWrapper(
                $tree,
                $rootHeaderLabel
            );
        }
    }

    /**
     * @param string $rootHeaderLabel See {@link ParseErrorsWrapper::getRootHeaderLabel()}.
     * @return T The constructor of object should have 0 arguments.
     * @throws ParseErrorsWrapper
     */
    public function copyToNewObject(
        string $rootHeaderLabel
    ) : object {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            self::invalidStructure( // Failed to create a new object from reflection.
                new StructureError(
                    "Failed to create a new object from reflection",
                    $err
                ),
                $this->getReflection()
            );

            throw new AssertionError("unreachable"); // Blame PHPStan.
        }
        $this->copyToObject(
            $instance,
            $rootHeaderLabel
        );
        return $instance;
    }

    /**
     * @return array<string, mixed[]>
     */
    public function getErrorsTree() : array
    {
        $tree = [];
        $propertyContexts = $this->getPropertyContexts();
        foreach ($propertyContexts as $propertyContext) {
            $tree = array_merge(
                $tree,
                $propertyContext->getWrappedErrorsTree()
            );
        }

        return $tree;
    }

    public function hasError() : bool
    {
        $tree = $this->getErrorsTree();
        return $tree !== [];
    }
}