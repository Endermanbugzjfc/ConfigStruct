<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;


use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use function assert;

#[Attribute(Attribute::TARGET_PROPERTY)] class ChildStruct
{

    /**
     * @phpstan-param class-string $class
     */
    public function __construct(?string $class)
    {
    }

    public static function initializeProperty(&$value, ReflectionProperty $property) : bool
    {
        if (AttributeUtils::trueIfNoInProperty(
            $property,
            self::class,
            $attribute
        )) {
            return false;
        }
        assert($attribute instanceof ReflectionAttribute);

        if (!isset($attribute->getArguments()[0])) {
            if ($property->getType() instanceof ReflectionNamedType) {
                $class = $property->getName();
            }
        } else {
            $class = $attribute->getArguments()[0];
        }
        if (isset($class) and class_exists($class)) {
            $value = new $class;
            foreach (
                (new ReflectionClass($value))->getProperties()
                as $sProperty
            ) {
                self::initializeProperty($sValue, $sProperty);
                $sName = $sProperty->getName();
                $value->$sName = $sValue;
            }
            return true;
        }
        return false;
    }

}