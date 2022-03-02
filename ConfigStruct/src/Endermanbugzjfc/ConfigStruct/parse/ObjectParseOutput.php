<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

final class ObjectParseOutput
{

    /**
     * @param ReflectionClass $reflection Reflection of the struct.
     * @param PropertyParseOutput[] $propertiesOutput Key = property name.
     * @param array<string, array> $unhandledElements
     * @param ReflectionProperty[] $missingElements Key = property name.
     */
    private function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertiesOutput,
        protected array           $unhandledElements,
        protected array           $missingElements
    )
    {
    }

    /**
     * @param ReflectionClass $reflection Reflection of the struct.
     * @param PropertyParseOutput[] $propertiesOutput Key = Property name.
     * @param array<string, array> $unhandledElements
     * @param ReflectionProperty[] $missingElements Key = Property name.
     * @return ObjectParseOutput
     */
    public static function create(
        ReflectionClass $reflection,
        array           $propertiesOutput,
        array           $unhandledElements,
        array           $missingElements
    ) : self
    {
        return new self(
            $reflection,
            $propertiesOutput,
            $unhandledElements,
            $missingElements
        );
    }

    /**
     * @return ReflectionClass Reflection of the struct.
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array<string, array>
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
     * @return ReflectionProperty[] Key = property name.
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * @param object $object
     * @return object === $object.
     */
    public function copyValuesToObject(
        object $object
    ) : object
    {
        foreach ($this->getPropertiesOutput() as $property) {
            $property->copyToObject(
                $object
            );
        }
        return $object;
    }

    /**
     * @return object The constructor of object should have 0 arguments.
     * @throws StructureError Failed to construct a new instance (probably incompatible arguments).
     */
    public function copyValuesToNewObject() : object
    {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            throw new StructureError($err);
        }
        return $this->copyValuesToObject(
            $instance
        );
    }

}