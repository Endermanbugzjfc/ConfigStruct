<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ScalarStructParseOutput extends PropertyParseOutput
{

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

    protected function getFlattenedValue() : bool|int|float|string
    {
        return $this->output;
    }
}