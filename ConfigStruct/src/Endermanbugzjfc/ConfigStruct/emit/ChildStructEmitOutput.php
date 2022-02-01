<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Endermanbugzjfc\ConfigStruct\parse\PropertyParseOutput;
use ReflectionProperty;

class ChildStructEmitOutput extends PropertyParseOutput
{

    protected function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        StructEmitOutput   $output
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

    protected function getFlattenedValue() : mixed
    {
        return $this->getChildStruct()->getFlattenedValue();
    }

    public function getChildStruct() : StructEmitOutput
    {
        return $this->output;
    }
}