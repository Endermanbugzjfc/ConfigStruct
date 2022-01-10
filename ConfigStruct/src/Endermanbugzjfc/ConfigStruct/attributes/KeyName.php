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
        bool|int|float|string $name
    )
    {
    }

    /**
     * @param ReflectionProperty $property
     * @return bool|int|float|string|null
     */
    public static function getFromProperty(
        ReflectionProperty $property
    ) : bool|int|float|string|null
    {
        $attribute = $property->getAttributes(self::class)[0] ?? null;
        if ($attribute === null) {
            return $property->getName();
        }

        return $attribute->getArguments()[0];
    }

}