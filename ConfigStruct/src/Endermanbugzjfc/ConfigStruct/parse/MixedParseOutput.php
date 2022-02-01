<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class MixedParseOutput extends PropertyParseOutput
{

    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output,
        array              $exceptions
    ) : PropertyParseOutput
    {
        return new self(
            $reflection,
            $keyName,
            $output,
            $exceptions
        );
    }

    protected function getFlattenedValue() : mixed
    {
        return $this->output;
    }
}