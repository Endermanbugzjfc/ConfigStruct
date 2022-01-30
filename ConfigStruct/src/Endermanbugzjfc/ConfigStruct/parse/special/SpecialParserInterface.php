<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseTimeProperty;

interface SpecialParserInterface
{

    public function isParserForProperty(
        ParseTimeProperty $property
    ) : bool;

    public function parseValue(
        array $value
    ) : mixed;

}