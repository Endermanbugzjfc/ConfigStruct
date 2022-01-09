<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\MissingFieldsException;

/**
 * When a property has this attribute, the corresponding field will be included in a {@link MissingFieldsException}. The exception will be thrown the parsing process has completed.
 *
 * This attribute has higher priority than {@link AutoInitializeChildStruct}.
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public function __construct(callable $onMissing)
    {
    }

}