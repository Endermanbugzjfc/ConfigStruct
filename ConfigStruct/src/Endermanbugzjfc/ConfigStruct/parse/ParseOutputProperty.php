<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\parse\special\SpecialParserInterface;
use ReflectionException;

final class ParseOutputProperty
{

    /**
     * @param ParseProperty $property
     * @param string $keyName
     * @param mixed $output
     * @param SpecialParserInterface|null $specialParser
     */
    private function __construct(
        protected ParseProperty           $property,
        protected string                  $keyName,
        protected mixed                   $output,
        protected ?SpecialParserInterface $specialParser
    )
    {
    }

    /**
     * @param ParseProperty $property
     * @param string $keyName
     * @param mixed $output
     * @param SpecialParserInterface|null $specialParser
     * @return ParseOutputProperty
     */
    public static function create(
        ParseProperty           $property,
        string                  $keyName,
        mixed                   $output,
        ?SpecialParserInterface $specialParser
    ) : self
    {
        return new self(
            $property,
            $keyName,
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
     * @return ParseProperty
     */
    public function getProperty() : ParseProperty
    {
        return $this->property;
    }

    public function getChildStructOutput() : ?ParseOutputStruct
    {
        return $this->output instanceof ParseOutputStruct
            ? $this->output
            : null;
    }

    /**
     * @throws ReflectionException
     */
    public function getFinalizedOutput() : mixed
    {
        $childStruct = $this->getChildStructOutput();
        if ($childStruct !== null) {
            return $childStruct->copyValuesToNewObject();
        } // TODO: Typed array
        return $this->output;
    }

}