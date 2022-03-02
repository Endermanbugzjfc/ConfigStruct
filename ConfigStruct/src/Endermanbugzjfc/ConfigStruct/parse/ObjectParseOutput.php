<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

final class ObjectParseOutput
{

    /**
     * @param ReflectionClass $reflection
     * @param PropertyParseOutput[] $propertiesOutput Key = property name.
     * @param array $unhandledElements Raw value of elements in the input which do not have the corresponding property.
     * @param ReflectionProperty[] $missingElements Key = property name.
     */
    public function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertiesOutput,
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
     * @return PropertyParseOutput[] Key = property name.
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * Copy output data to the given object. Without checking the compatibility.
     *
     * It is your job to ensure you had passed the correct object. If you do not want to deal with this, considering using {@link ObjectParseOutput::copyToNewObject()}.
     * @param object $object This object will be modified.
     * @return object The same object as the above argument.
     */
    public function copyToObject(
        object $object
    ) : object
    {
        $properties = $this->getPropertiesOutput();
        foreach ($properties as $name => $property) {
            $object->$name = $property;
        }
        return $object;
    }

    /**
     * Copy output data to an new object.
     * @return object The constructor of object should have 0 arguments.
     * @throws StructureError Failed to construct a new instance (probably incompatible arguments).
     */
    public function copyToNewObject() : object
    {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            throw new StructureError($err);
        }
        return $this->copyToObject(
            $instance
        );
    }

}