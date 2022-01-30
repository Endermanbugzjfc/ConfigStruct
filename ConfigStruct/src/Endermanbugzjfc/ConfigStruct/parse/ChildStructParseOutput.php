<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    /**
     * @param ReflectionProperty $reflection
     * @param string|null $keyName
     * @param mixed $output
     */
    protected function __construct(
        ReflectionProperty $reflection,
        string             $keyName = null,
        StructParseOutput  $output = null
    )
    {
        if ($keyName !== null) {
            parent::__construct(
                $reflection,
                $keyName,
                $output
            );
        }
    }

    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output
    ) : PropertyParseOutput
    {
        return new self(
            $reflection,
            $keyName,
            $output
        );
    }

    public static function indicator(
        ReflectionProperty $reflection
    ) : PropertyParseOutput
    {
        return new self(
            $reflection
        );
    }

    /**
     * @return StructParseOutput
     */
    public function getChildStruct() : StructParseOutput
    {
        return $this->output;
    }

    protected function getFlattenedValue() : object
    {
        return $this->getChildStruct()->copyValuesToNewObject();
    }

    public function isIndicator() : bool
    {
        return !isset($this->keyName);
    }

}