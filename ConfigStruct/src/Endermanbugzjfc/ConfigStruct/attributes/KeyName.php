<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use ReflectionProperty;

/**
 * Emit and parse this property with the name provided in the attribute instead of the property name.
 *
 * If multiple names are provided, only the first one is used for emitting, and the first name available in the input is used for parsing.
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
     * @return int|string The property name if it doesn't have this attribute, or else the key name specified in the attribute argument.
     */
    public static function getFromProperty(
        ReflectionProperty $property
    ) : int|string
    {
        $attribute = $property->getAttributes(self::class)[0] ?? null;
        if ($attribute === null) {
            return $property->getName();
        }

        return $attribute->getArguments()[0];
    }

}