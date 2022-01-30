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

    /**
     * @return string
     */
    public function getPropertyName() : string
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getKeyName() : string
    {
        return $this->keyName;
    }

    /**
     * @return mixed
     */
    public function getOutput() : mixed
    {
        return $this->output;
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