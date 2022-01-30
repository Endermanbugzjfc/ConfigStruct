<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;

final class ParseOutputProperty
{

    /**
     * @param array $rawOutput
     * @param ParseOutputStruct[] $childStructs
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
     * @param ParseOutputStruct[] $childStructs
     * @param SpecialParserInterface|null $specialParser
     * @return ParseOutputProperty
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