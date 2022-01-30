<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;

final class ParseOutputProperty
{

    /**
     * @param array $finalizedValues
     * @param ParseOutputStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     */
    private function __construct(
        protected array                   $finalizedValues,
        protected array                   $childStructs,
        protected ?SpecialParserInterface $specialParser
    )
    {
    }

    /**
     * @param array $finalizedValues
     * @param ParseOutputStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     * @return ParseOutputProperty
     */
    public static function create(
        array                   $finalizedValues,
        array                   $childStructs,
        ?SpecialParserInterface $specialParser
    ) : self
    {
        return new self(
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

}