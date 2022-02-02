<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionClass;
use ReflectionProperty;

final class StructEmitOutput
{

    /**
     * @param ReflectionClass $reflection Reflection of the struct.
     * @param PropertyEmitOutput[] $propertiesOutput Key = property name and NOT KEY NAME.
     * @param ReflectionProperty[] $skippedEmptyProperties Key = property name.
     */
    private function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertiesOutput,
        protected array           $skippedEmptyProperties
    )
    {
    }

    /**
     * @param ReflectionClass $reflection Reflection of the struct.
     * @param PropertyEmitOutput[] $propertiesOutput Key = property name and NOT KEY NAME.
     * @param ReflectionProperty[] $skippedEmptyProperties Key = property name.
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
     * @return PropertyEmitOutput[] Key = property name and NOT KEY NAME.
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * @return ReflectionProperty[] Key = property name.
     */
    public function getSkippedEmptyProperties() : array
    {
        return $this->skippedEmptyProperties;
    }

    /**
     * @return array Key = key name.
     */
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