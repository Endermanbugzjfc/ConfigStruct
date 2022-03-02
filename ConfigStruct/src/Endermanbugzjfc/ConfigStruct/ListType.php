<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

/**
 * TODO
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ListType
{

    public function __construct(
        string $type
    )
    {
    }

}