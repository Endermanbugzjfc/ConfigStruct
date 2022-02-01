<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\StructureException;
use ReflectionClass;
use ReflectionException;

final class StructParseOutput
{

    /**
     * @param ReflectionClass $reflection
     * @param PropertyParseOutput[] $propertiesOutput
     * @param array $unhandledElements
     * @param string[] $missingElements
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
     * @param ReflectionClass $reflection
     * @param PropertyParseOutput[] $propertiesOutput
     * @param array $unhandledElements
     * @param string[] $missingElements
     * @return StructParseOutput
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
     * @return ReflectionClass
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array
     */
    public function getUnhandledElements() : array
    {
        return $this->unhandledElements;
    }

    /**
     * @return string[]
     */
    public function getMissingElements() : array
    {
        return $this->missingElements;
    }

    /**
     * @return PropertyParseOutput[]
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

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

    public function copyValuesToNewObject() : object
    {
        try {
            return $this->copyValuesToObject(
                $this->getReflection()->newInstance()
            );
        } catch (ReflectionException $err) {
            throw new StructureException($err);
        }
    }

}