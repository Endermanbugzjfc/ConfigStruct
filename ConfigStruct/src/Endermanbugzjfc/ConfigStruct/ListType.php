<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;
use Endermanbugzjfc\ConfigStruct\ParseError\TypeMismatchError;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ListType
{

    /**
     * Struct candidate for elements in a list.
     *
     * If multiple candidates are provided, only the one with 0 errors and least unhandled elements will be used. If there is no available struct for an element, the first one will be used. (And receive an {@link TypeMismatchError}).
     * @param string $type Class name of the struct candidate. If the class had become invalid during runtime, this candidate will be omitted.
     */
    public function __construct(
        string $type // TODO: Dynamic.
    )
    {
    }

}