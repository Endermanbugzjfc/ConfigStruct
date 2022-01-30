<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;

final class ParseOutputProperty
{

    /**
     * @param ParseTimeProperty $property
     * @param mixed $output
     * @param SpecialParserInterface|null $specialParser
     */
    private function __construct(
        protected ParseTimeProperty       $property,
        protected mixed                   $output,
        protected ?SpecialParserInterface $specialParser
    )
    {
    }

    /**
     * @param ParseTimeProperty $property
     * @param mixed $output
     * @param SpecialParserInterface|null $specialParser
     * @return ParseOutputProperty
     */
    public static function create(
        ParseTimeProperty       $property,
        mixed                   $output,
        ?SpecialParserInterface $specialParser
    ) : self
    {
        return new self(
            $property,
            $output,
            $specialParser
        );
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