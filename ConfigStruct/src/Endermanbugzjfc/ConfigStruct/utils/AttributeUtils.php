<?php

namespace Endermanbugzjfc\ConfigStruct\utils;

use ReflectionAttribute;
use ReflectionProperty;

final class AttributeUtils
{

    /**
     * You shouldn't create an instance of this class, just run the APIs statically.
     */
    private function __construct()
    {
    }

    /**
     * @param ReflectionProperty $property The property to check whether it has an instance of the given attribute.
     * @param string $attribute The class string of an attribute.
     * @phpstan-param class-string<Attribute>
     * @param $value ReflectionAttribute A variable reference, the reflection instance of the given attribute will be assigned to this reference if found.
     * @return bool True = the property doesn't have an instance of the given attribute.
     * @noinspection PhpMissingParamTypeInspection stfu phpsjorm.
     */
    public static function trueIfNoInProperty(
        ReflectionProperty $property,
        string             $attribute,
                           &$value = null
    ) : bool
    {
        $attrib = $property->getAttributes($attribute)[0] ?? null;
        if (!isset($attrib)) {
            return false;
        }
        $value = $attrib;
        return true;
    }

}