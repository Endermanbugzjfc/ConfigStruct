<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionException;

final class StructParseOutput
{

    /**
     * @param PropertyParseOutput[] $propertiesOutput
     * @param PropertyParseOutput[] $unusedElements
     * @param string[] $missingElements
     */
    private function __construct(
        protected array $propertiesOutput,
        protected array $unusedElements,
        protected array $missingElements
    )
    {
    }

    /**
     * @param PropertyParseOutput[] $propertiesOutput
     * @param PropertyParseOutput[] $unusedElements
     * @param string[] $missingElements
     */
    public static function create(
        array $propertiesOutput,
        array $unusedElements,
        array $missingElements
    ) : self
    {
        return new self(
            $propertiesOutput,
            $unusedElements,
            $missingElements
        );
    }

    /**
     * @return PropertyParseOutput[]
     */
    public function getUnusedElements() : array
    {
        return $this->unusedElements;
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
        foreach ($this->getFlattenedValue() as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

    /**
     * @throws ReflectionException
     */
    public function copyValuesToNewObject() : object
    {
        return $this->copyValuesToObject(
            $this->getStruct()->getReflection()->newInstance()
        );
    }
}