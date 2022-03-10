<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseError\TypeMismatchError;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use Endermanbugzjfc\ConfigStruct\StructureError;
use Endermanbugzjfc\ConfigStruct\Utils\StructureErrorThrowerTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use TypeError;
use function array_map;
use function array_merge;
use function array_unique;
use function class_exists;
use function get_debug_type;

final class ObjectContext
{
    use StructureErrorThrowerTrait;

    // TODO: Fix document for $errors, cannot be rendered by PHPStorm correctly.
    /**
     * @param ReflectionClass $reflection
     * @param BasePropertyContext[] $propertyContexts Key = property name.
     * @param array $unhandledElements Raw value of elements in the input which do not have the corresponding property.
     * @param ReflectionProperty[] $missingElements Key = property name.
     */
    public function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertyContexts,
        protected array           $unhandledElements,
        protected array           $missingElements
    )
    {
    }

    /**
     * @return ReflectionClass
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array Raw value of elements in the input which do not have the corresponding property. Please notice that some properties might also have their unhandled elements, see {@link BasePropertyContext::getUnhandledElements()}.
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
     * @return object The same object as the first argument. So it can be used fluently (in chain of function calls).
     * @throws ParseErrorsWrapper
     */
    public function copyToObject(
        object $object,
        string $rootHeaderLabel
    ) : object
    {
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
                $types = $reflection->getType();
                $expectedTypes = array_unique(
                    array_map(
                        fn(ReflectionType $type) : string => (
                            class_exists($raw = $type->getName())
                            or
                            $raw === "self"
                        )
                            ? "array"
                            : $raw,
                        $types instanceof ReflectionNamedType
                            ? [$types]
                            : $types->getTypes()
                    )
                );
                if ($types->allowsNull()) {
                    $expectedTypes[] = "null";
                }

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
        return $object;
    }

    /**
     * @param string $rootHeaderLabel See {@link ParseErrorsWrapper::getRootHeaderLabel()}.
     * @return object The constructor of object should have 0 arguments.
     * @throws ParseErrorsWrapper
     */
    public function copyToNewObject(
        string $rootHeaderLabel
    ) : object
    {
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
        }
        $this->copyToObject(
            $instance,
            $rootHeaderLabel
        );
        return $instance;
    }

    /**
     * @return array
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