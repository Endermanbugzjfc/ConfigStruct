<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public function __construct(callable $onMissing)
    {
    }

}