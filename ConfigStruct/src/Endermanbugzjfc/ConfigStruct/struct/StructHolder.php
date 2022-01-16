<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

final class StructHolder implements StructHolderInterface
{

    private function __construct(
        protected string $class
    )
    {
    }

    public static function newStructHolder(object $struct) : self
    {
        return new self($struct::class);
    }

    public function newStructForParsing() : object
    {
        return new $this->class;
    }

    public function newStructForEmitting() : object
    {
        return new $this->class;
    }

}