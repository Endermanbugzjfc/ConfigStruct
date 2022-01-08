<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
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
        $value = $property->getName();

        $keyName = $property->getAttributes(KeyName::class)[0] ?? null;
        if (!isset($keyName)) {
            return false;
        }
        $value = $keyName->getArguments()[0];
        return true;
    }

}