<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class ChildStructParseOutput implements ValueOutputInterface
{

    private function __construct(
        protected ParseOutputStruct $childStruct
    )
    {
    }

    public static function create(
        ParseOutputStruct $childStruct
    ) : self
    {
        return new self(
            $childStruct
        );
    }

}