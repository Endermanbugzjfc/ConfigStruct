<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use ReflectionProperty;

/**
 * > TL;DR:
 * > Set custom key name for a field (if you don't want to use the property name).
 * > Key names behind the first one will be used for remapping during parse (useful for config updating).
 *
 * When a property has this attribute, its corresponding field will use the key names given in the attribute arguments instead of the property name.
 *
 * You can provide as many key names as you want.
 * During parse, Field searching runs in the order of given key names. It starts at the first key name. If no fields use that key name, it look for the next one. The field search stops once a field has been found. Please aware that the property name will not be used in field search unless it was given in the attribute arguments.
 * During emit, the property's corresponding field will use ONLY the first key name.
 *
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