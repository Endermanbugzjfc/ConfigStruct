<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionAttribute;
use ReflectionProperty;

/**
 * When a property has this attribute, its corresponding field will use the name given in the instance of this attribute instead of the property name.
 *
 * Unlimited key names (arguments) could be provided in the attribute. Each will be used for a deeper nesting level. Example: "a", "b" is actually [a => b].
 *
 * The nesting system can work with other properties too, the value won't be override unless the exact same key names are used in two different attributes, or two nodes have different data types.
 * TODO: Examples
 */
#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        bool|int|float|string $name
    ) // TODO: Update nesting system
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