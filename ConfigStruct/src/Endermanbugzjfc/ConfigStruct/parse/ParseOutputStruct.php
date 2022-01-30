<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionException;

final class ParseOutputStruct
{

    /**
     * @param ParseStruct $struct
     * @param PropertyParseOutput[] $propertiesOutput
     * @param PropertyParseOutput[] $unusedElements
     * @param ParseProperty[] $missingElements
     */
    private function __construct(
        protected ParseStruct $struct,
        protected array       $propertiesOutput,
        protected array       $unusedElements,
        protected array       $missingElements
    )
    {
    }

    /**
     * @param ParseStruct $struct
     * @param PropertyParseOutput[] $propertiesOutput
     * @param PropertyParseOutput[] $unusedElements
     * @param ParseProperty[] $missingElements
     * @return static
     */
    public static function create(
        ParseStruct $struct,
        array       $propertiesOutput,
        array       $unusedElements,
        array       $missingElements
    ) : self
    {
        return new self(
            $struct,
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
     * @return ParseProperty[]
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

    /**
     * @return ParseStruct
     */
    public function getStruct() : ParseStruct
    {
        return $this->struct;
    }

    protected function getFinalizedOutput() : array
    {
        foreach ($this->getPropertiesOutput() as $property) {
            $return[$property
                ->getProperty()
                ->getReflection()
                ->getName()]
                = $property->getFinalizedOutput();
        }
        return $return ?? [];
    }

    public function copyValuesToObject(
        object $object
    ) : object
    {
        foreach ($this->getFinalizedOutput() as $name => $value) {
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