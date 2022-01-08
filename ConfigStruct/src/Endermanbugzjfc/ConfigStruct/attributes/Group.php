<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;


use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)] class Group
{

    /**
     * @phpstan-param class-string $class
     */
    public function __construct(?string $class)
    {
    }

    public static function emitGroup(
        object             $struct,
                           &$value,
        ReflectionProperty $property
    ) : bool
    {
        if (AttributeUtils::trueIfNo(
            $property,
            self::class,
            $attribute
        )) {
            return false;
        }
        assert($attribute instanceof ReflectionAttribute);

        $child = $property->getValue($struct);
        $value = (array)$child;
        foreach (
            (new ReflectionClass($child))
                ->getProperties()
            as $sProperty
        ) {
            if (self::emitGroup($child, $sValue, $sProperty)) {
                $value[$sProperty->getName()] = $sValue;
            }
        }
        return true;
    }


}