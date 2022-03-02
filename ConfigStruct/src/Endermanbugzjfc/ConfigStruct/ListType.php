<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

/**
 * TODO
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