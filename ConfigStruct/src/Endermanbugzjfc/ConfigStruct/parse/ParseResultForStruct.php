<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class ParseResultForStruct
{

    /**
     * @param ParseResultForProperty[] $properties
     * @param array $unusedElements
     * @param ParseTimeProperty[] $missingElements
     */
    private function __construct(
        protected array $properties,
        protected array $unusedElements,
        protected array $missingElements
    )
    {
    }

    /**
     * @param ParseResultForProperty[] $properties
     * @param array $unusedElements
     * @param ParseTimeProperty[] $missingElements
     * @return static
     */
    public static function create(
        array $properties,
        array $unusedElements,
        array $missingElements
    ) : self
    {
        return new self(
            $properties,
            $unusedElements,
            $missingElements
        );
    }

    /**
     * @return array
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
     * @return ParseResultForProperty[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

}