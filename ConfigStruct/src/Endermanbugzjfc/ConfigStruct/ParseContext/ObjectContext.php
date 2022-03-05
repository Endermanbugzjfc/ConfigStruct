<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;
use TypeError;
use function array_merge;

final class ObjectContext
{

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
     * @return array Raw value of elements in the input which do not have the corresponding property.
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
     * @return Throwable[][] array<string, list<Throwable>>. Mostly {@link TypeError} or other parse time errors. Key = property name.
     */
    public function copyToObject(
        object $object
    ) : array
    {
        $properties = $this->getPropertyContexts();
        foreach ($properties as $name => $property) {
            $errs[$name] = $property->getErrorsTree();
            try {
                $property->getDetails()->getReflection()->setValue(
                    $object,
                    $property->getValue()
                );
            } catch (TypeError $err) {
                $errs[$name][] = $err;
            }
        }
        return $errs ?? [];
    }

    /**
     * Copy output data to an new object.
     * @param array|null $errs Reference parameter, use this to retrieve the errors from {@link ObjectContext::copyToObject()}.
     * @return object The constructor of object should have 0 arguments.
     * @throws StructureError Failed to construct a new instance (probably incompatible arguments).
     */
    public function copyToNewObject(
        ?array &$errs = null
    ) : object
    {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            throw new StructureError($err);
        }
        $errs = $this->copyToObject(
            $instance
        );
        return $instance;
    }

    /**
     * @return array
     */
    public function getErrorsTree() : array
    {
        $tree = [];
        $properties = $this->getPropertyContexts();
        foreach ($properties as $property) {
            array_merge(
                $tree,
                $property->getWrappedErrorsTree()
            );
        }

        return $tree;
    }

    public function hasError() : bool
    {
        $tree = $this->getErrorsTree();
        return !empty(
        $tree
        );
    }

}