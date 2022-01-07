<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Endermanbugzjfc\ConfigStruct\Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        bool|int|float|string $name
    )
    {
    }

}