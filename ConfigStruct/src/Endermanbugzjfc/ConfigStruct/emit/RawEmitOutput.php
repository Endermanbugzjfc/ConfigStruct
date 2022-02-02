<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionProperty;

final class RawEmitOutput extends PropertyEmitOutput
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

    /**
     * @return mixed Returns the output directly without any special logic and modifications.
     */
    public function getFlattenedValue() : mixed
    {
        return $this->output;
    }
}