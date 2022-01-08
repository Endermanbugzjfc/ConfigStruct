<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionAttribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        bool|int|float|string $name
    )
    {
    }

    public static function getFromProperty(
        &$value,
        ReflectionProperty $property
    ) : bool
    {
        if (AttributeUtils::trueIfNoInProperty(
            $property,
            self::class,
            $attribute
        )) {
            return false;
        }
        assert($attribute instanceof ReflectionAttribute);


        $value = $property->getName();
        $value = $attribute->getArguments()[0];
        return true;
    }

}