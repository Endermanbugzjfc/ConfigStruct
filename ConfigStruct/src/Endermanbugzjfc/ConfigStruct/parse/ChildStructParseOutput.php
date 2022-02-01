<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     */
    protected function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        StructParseOutput  $output
    )
    {
        parent::__construct(
            $reflection,
            $keyName,
            $output
        );
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

}