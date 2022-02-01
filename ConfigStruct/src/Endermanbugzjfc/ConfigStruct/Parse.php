<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\TypedArray;
use Endermanbugzjfc\ConfigStruct\parse\StructParseOutput;
use Endermanbugzjfc\ConfigStruct\parse\StructParser;
use ReflectionClass;

final class Parse
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    public static function array(
        object $struct,
        array  $input
    ) : StructParseOutput
    {
        $output = StructParser::parseStruct(
            new ReflectionClass($struct),
            $input
        );
        $output->copyValuesToObject($struct);
    }

}