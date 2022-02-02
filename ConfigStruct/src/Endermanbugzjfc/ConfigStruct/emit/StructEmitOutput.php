<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionClass;
use ReflectionProperty;

final class StructEmitOutput
{

    /**
     * @param ReflectionClass $reflection
     * @param PropertyEmitOutput[] $propertiesOutput
     * @param ReflectionProperty[] $skippedEmptyProperties
     */
    private function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertiesOutput,
        protected array           $skippedEmptyProperties
    )
    {
    }

    /**
     * @param ReflectionClass $reflection
     * @param PropertyEmitOutput[] $propertiesOutput
     * @param ReflectionProperty[] $skippedEmptyProperties
     * @return StructEmitOutput
     */
    public static function create(
        ReflectionClass $reflection,
        array           $propertiesOutput,
        array           $skippedEmptyProperties
    ) : self
    {
        return new self(
            $reflection,
            $propertiesOutput,
            $skippedEmptyProperties
        );
    }

    /**
     * @return ReflectionClass
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return PropertyEmitOutput[]
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getSkippedEmptyProperties() : array
    {
        return $this->skippedEmptyProperties;
    }

    public function getFlattenedValue() : array
    {
        foreach (
            $this->getPropertiesOutput()
            as $property
        ) {
            $return[$property->getKeyName()] = $property->getFlattenedValue();
        }
        return $return ?? [];
    }

}