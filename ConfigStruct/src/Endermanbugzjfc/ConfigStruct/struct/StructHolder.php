<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

final class StructHolder implements StructHolderInterface
{

    private function __construct(
        protected object $struct
    )
    {
    }

    public static function newStructHolder(object $struct) : self
    {
        return new self($struct);
    }

    public function newStructForParsing() : object
    {
        return $this->struct;
    }
}