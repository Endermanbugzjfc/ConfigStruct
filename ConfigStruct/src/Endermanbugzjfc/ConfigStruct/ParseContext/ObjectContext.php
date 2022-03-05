<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseError;
use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
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
     * @param string $rootHeaderLabel See {@link ParseError::getRootHeaderLabel()}.
     * @return object The same object as the first argument. So it can be used fluently (in chain of function calls).
     * @throws ParseError
     */
    public function copyToObject(
        object $object,
        string $rootHeaderLabel
    ) : object
    {
        $properties = $this->getPropertyContexts();
        $errs = $this->getErrorsTree();
        foreach ($properties as $property) {
            try {
                $property->getDetails()->getReflection()->setValue(
                    $object,
                    $property->getValue()
                );
            } catch (TypeError $err) {
                $treeKey = $property->getErrorsTreeKey();
                $errs[$treeKey][] = $err;
            }
        }

        if (!empty(
        $errs
        )) {
            throw new ParseError(
                $errs,
                $rootHeaderLabel
            );
        }
        return $object;
    }

    /**
     * @param string $rootHeaderLabel See {@link ParseError::getRootHeaderLabel()}.
     * @return object The constructor of object should have 0 arguments.
     * @throws StructureError|ParseError StructureError = Failed to construct a new instance (probably incompatible arguments).
     */
    public function copyToNewObject(
        string $rootHeaderLabel
    ) : object
    {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            throw new StructureError($err);
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