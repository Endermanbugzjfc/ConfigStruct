<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class ParseResultForProperty
{

    private function __construct()
    {
    }

    public static function create() : self
    {
        return new self();
    }

}