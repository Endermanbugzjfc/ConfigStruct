<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\MissingFieldsException;

/**
 * When a property has this attribute, its corresponding field will be included in a {@link MissingFieldsException}. The exception will be thrown the parsing process has completed.
 *
 * This attribute has higher priority than {@link ChildStruct}. The child struct of a missing field will NOT be initialized.
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public function __construct(callable $onMissing)
    {
    }

}