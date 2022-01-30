<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseProperty;

interface SpecialParserInterface
{

    public function isParserForProperty(
        ParseProperty $property
    ) : bool;

    public function isParserForValue(
        ParseProperty $property,
        array         $value
    ) : bool;

    public function parseValue(
        ParseProperty $property,
        array         $value
    ) : mixed;

}