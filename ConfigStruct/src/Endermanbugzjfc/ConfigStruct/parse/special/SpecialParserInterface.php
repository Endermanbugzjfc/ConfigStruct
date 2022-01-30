<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseTimeProperty;

interface SpecialParserInterface
{

    public function isParserForProperty(
        ParseTimeProperty $property
    ) : bool;

    public function isParserForValue(
        ParseTimeProperty $property,
        array             $value
    ) : bool;

    public function parseValue(
        ParseTimeProperty $property,
        array             $value
    ) : mixed;

}