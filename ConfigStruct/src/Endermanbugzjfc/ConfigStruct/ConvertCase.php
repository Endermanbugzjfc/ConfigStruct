<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

/**
 * Walk selected key names in a class with the case converter when emitting or parsing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ConvertCase
{

    public function __construct(
        callable $caseConverter
    )
    {
    }

}