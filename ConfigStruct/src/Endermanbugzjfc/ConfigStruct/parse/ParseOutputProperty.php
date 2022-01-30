<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;

final class ParseOutputProperty
{

    /**
     * @param ParseTimeProperty $property
     * @param array $finalizedValues
     * @param ParseOutputStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     */
    private function __construct(
        protected ParseTimeProperty       $property,
        protected array                   $finalizedValues,
        protected array                   $childStructs,
        protected ?SpecialParserInterface $specialParser
    )
    {
    }

    /**
     * @param ParseTimeProperty $property
     * @param array $finalizedValues
     * @param ParseOutputStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     * @return ParseOutputProperty
     */
    public static function create(
        ParseTimeProperty       $property,
        array                   $finalizedValues,
        array                   $childStructs,
        ?SpecialParserInterface $specialParser
    ) : self
    {
        return new self(
            $property,
            $finalizedValues,
            $childStructs,
            $specialParser
        );
    }

    /**
     * @return ParseOutputStruct[]
     */
    public function getChildStructs() : array
    {
        return $this->childStructs;
    }

    /**
     * @return SpecialParserInterface|null
     */
    public function getSpecialParser() : ?SpecialParserInterface
    {
        return $this->specialParser;
    }

    /**
     * @return ParseTimeProperty
     */
    public function getProperty() : ParseTimeProperty
    {
        return $this->property;
    }

}