<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Endermanbugzjfc\ConfigStruct\parse\PropertyParseOutput;
use ReflectionProperty;

final class RawStructEmitOutput extends PropertyParseOutput
{

    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed $output
    ) : PropertyParseOutput
    {
        return new self(
            $reflection,
            $keyName,
            $output
        );
    }

    protected function getFlattenedValue() : mixed
    {
        return $this->output;
    }
}