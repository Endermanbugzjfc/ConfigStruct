<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class KeyName
{

    /**
     * Emit and parse this property with the name provided in the attribute instead of the property name.
     *
     * If multiple names are provided, only the first one is used for emitting, and the first name available in the input (the first key that exists in the parse data) is used for parsing. This gets useful when updating configs.
     *
     * @param bool $reserveTextCase True = this key name will not be affected by the {@link ConvertCase} attribute.
     */
    public function __construct(
        int|string $name,
        bool $reserveTextCase = false, // TODO: Add test.
    ) {
    }
}