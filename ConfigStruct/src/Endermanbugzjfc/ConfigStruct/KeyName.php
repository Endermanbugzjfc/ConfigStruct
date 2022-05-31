<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

/**
 * Emit and parse this property with the name provided in the attribute instead of the property name.
 *
 * If multiple names are provided, only the first one is used for emitting, and the first name available in the input (the first key that exists in the parse data) is used for parsing. This gets useful when updating configs.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class KeyName
{
    public function __construct(
        int|string $name,
    ) {
    }
}