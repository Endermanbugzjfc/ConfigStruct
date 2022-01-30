<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class ParseResultForStruct
{

    /**
     * @param array $unusedElements
     * @param ParseTimeProperty[] $missingElements
     */
    private function __construct(
        protected array $unusedElements,
        protected array $missingElements
    )
    {
    }

    /**
     * @param array $unusedElements
     * @param ParseTimeProperty[] $missingElements
     * @return static
     */
    public static function create(
        array $unusedElements,
        array $missingElements
    ) : self
    {
        return new self(
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

}