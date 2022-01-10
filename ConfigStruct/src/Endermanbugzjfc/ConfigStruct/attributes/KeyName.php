<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use ReflectionProperty;

/**
 * When a property has this attribute, its corresponding field will use the name given in the instance of this attribute instead of the property name.
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        int|string $name
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