<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;

final class ParseResultForProperty
{

    /**
     * @param array $rawOutput
     * @param ParseResultForStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     */
    private function __construct(
        protected array                   $rawOutput,
        protected array                   $childStructs,
        protected ?SpecialParserInterface $specialParser
    )
    {
    }

    /**
     * @param array $rawOutput
     * @param ParseResultForStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     * @return ParseResultForProperty
     */
    public static function create(
        array                   $rawOutput,
        array                   $childStructs,
        ?SpecialParserInterface $specialParser
    ) : self
    {
        return new self(
            $rawOutput,
            $childStructs,
            $specialParser
        );
    }

    /**
     * @return array
     */
    public function getRawOutput() : array
    {
        return $this->rawOutput;
    }

    /**
     * @return ParseResultForStruct[]
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