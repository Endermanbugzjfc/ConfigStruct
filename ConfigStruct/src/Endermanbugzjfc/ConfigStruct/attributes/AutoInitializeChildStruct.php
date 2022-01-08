<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;


use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionNamedType;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)] class AutoInitializeChildStruct
{

    /**
     * @phpstan-param class-string $class
     */
    public function __construct(?string $class = null)
    {
    }

    public static function initializeProperty(&$value, ReflectionProperty $property) : bool
    {
        if (AttributeUtils::trueIfNo(
            $property,
            self::class,
            $attribute
        )) {
            return false;
        }

        if (!isset($attribute->getArguments()[0])) {
            if ($property->getType() instanceof ReflectionNamedType) {
                $class = $property->getName();
            }
        } else {
            $class = $attribute->getArguments()[0];
        }
        if (isset($class) and class_exists($class)) {
            $value = new $class;
            return true;
        }
        return false;
    }

}