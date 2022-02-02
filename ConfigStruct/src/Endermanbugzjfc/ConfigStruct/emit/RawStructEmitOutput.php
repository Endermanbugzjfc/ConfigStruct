<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionProperty;

final class RawStructEmitOutput extends PropertyEmitOutput
{

    public static function create(
        ReflectionProperty $reflection,
        mixed              $output
    ) : PropertyEmitOutput
    {
        return new self(
            $reflection,
            $output
        );
    }

    public function getFlattenedValue() : mixed
    {
        return $this->output;
    }
}