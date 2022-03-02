<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

/**
 * Struct candidate for elements in a list.
 *
 * If multiple candidates are provided, only the one with least unhandled elements will be used. Incompatible structs will never be used. If there is no suitable struct for an element, that element will not be parsed, raw values will be returned.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ListType
{

    public function __construct(
        string $type
    )
    {
    }

}