<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ListType
{

    /**
     * Struct candidate for elements in a list.
     *
     * If multiple candidates are provided, only the one with least unhandled elements will be used. Incompatible structs will never be used. If there is no suitable struct for an element, that element will not be parsed, raw values will be returned.
     * @param string $type Class name of the struct candidate. If the class had become invalid during runtime, this candidate will be omitted.
     */
    public function __construct(
        string $type
    )
    {
    }

}