<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class PropertyParseOutput extends ParseOutput
{

    /**
     * @param string $propertyName
     * @param string $keyName
     * @param mixed $output
     */
    private function __construct(
        protected string $propertyName,
        protected string $keyName,
        protected mixed  $output
    )
    {
    }

    /**
     * @param string $propertyName
     * @param string $keyName
     * @param mixed $output
     * @return PropertyParseOutput
     */
    public static function create(
        string $propertyName,
        string $keyName,
        mixed  $output
    ) : self
    {
        return new self(
            $propertyName,
            $keyName,
            $output
        );
    }

    protected function getFlattenedValue() : mixed
    {
        $childStruct = $this->getChildStructOutput();
        if ($childStruct !== null) {
            return $childStruct->copyValuesToNewObject();
        } // TODO: Typed array
        return $this->output;
    }

}