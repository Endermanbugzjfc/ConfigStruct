<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\MissingFieldsException;

/**
 * When a property has this attribute, its corresponding field will be included in a {@link MissingFieldsException} if is missing during parse. The exception will be thrown the parsing process has completed.
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public function __construct()
    {
    }

}