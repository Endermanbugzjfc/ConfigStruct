<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionProperty;

class ChildStructEmitOutput extends PropertyEmitOutput
{

    /**
     * @inheritDoc
     */
    protected function __construct(
        ReflectionProperty $reflection,
        StructEmitOutput   $output
    )
    {
        parent::__construct(
            $reflection,
            $output
        );
    }

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

    public function getFlattenedValue() : array
    {
        return $this->getChildStruct()->getFlattenedValue();
    }

    public function getChildStruct() : StructEmitOutput
    {
        return $this->output;
    }
}