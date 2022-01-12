<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use ReflectionProperty;

/**
 * Emit and parse this property with the name provided in the attribute instead of the property name.
 *
 * If multiple names are provided, only the first one is used for emitting, and the first name available in the input is used for parsing. This gets useful when updating configs.
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        int|string ...$name
    )
    {
    }

    /**
     * @param ReflectionProperty $property
     * @return array<int|string> The property name if it doesn't have this attribute, or else the key name specified in the attribute argument.
     */
    public static function getFromProperty(
        ReflectionProperty $property
    ) : array
    {
        $names = $property->getAttributes(KeyName::class)[0]?->getArguments();
        return $names ?? [$property->getName()];
    }

}