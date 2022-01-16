<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

final class StructCache implements StructHolderInterface
{

    private function __construct()
    {
    }

    public static function newCache(string $class) : self
    {
        return new self($class);
    }

    public function newStructForParsing() : object
    {
        // TODO: Implement newStructForParsing() method.
    }
}