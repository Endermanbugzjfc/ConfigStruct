<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionException;

final class ParseOutputStruct
{

    /**
     * @param ParseTimeStruct $struct
     * @param ParseOutputProperty[] $propertiesOutput
     * @param ParseOutputProperty[] $unusedElements
     * @param ParseTimeProperty[] $missingElements
     */
    private function __construct(
        protected ParseTimeStruct $struct,
        protected array           $propertiesOutput,
        protected array           $unusedElements,
        protected array           $missingElements
    )
    {
    }

    /**
     * @param ParseTimeStruct $struct
     * @param ParseOutputProperty[] $propertiesOutput
     * @param ParseOutputProperty[] $unusedElements
     * @param ParseTimeProperty[] $missingElements
     * @return static
     */
    public static function create(
        ParseTimeStruct $struct,
        array           $propertiesOutput,
        array           $unusedElements,
        array           $missingElements
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
     * @return ParseOutputProperty[]
     */
    public function getUnusedElements() : array
    {
        return $this->unusedElements;
    }

    /**
     * @return ParseTimeProperty[]
     */
    public function getMissingElements() : array
    {
        return $this->missingElements;
    }

    /**
     * @return ParseOutputProperty[]
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * @return ParseTimeStruct
     */
    public function getStruct() : ParseTimeStruct
    {
        return $this->struct;
    }

    public function copyValuesToObject(
        object $object
    ) : object
    {
        foreach ($this->getProperties() as $property) {
            $property->copyValuesToObject($object);
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