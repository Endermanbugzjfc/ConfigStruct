<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseTimeProperty;

class ChildStructParser implements SpecialParserInterface
{

    public function isParserForProperty(ParseTimeProperty $property) : bool
    {
        // TODO: Implement isParserForProperty() method.
    }

    public function isParserForValue(
        ParseTimeProperty $property,
        array             $value
    ) : bool
    {
        // TODO: Implement isParserForValue() method.
    }

    public function parseValue(array $value) : mixed
    {
        // TODO: Implement parseValue() method.
    }
}