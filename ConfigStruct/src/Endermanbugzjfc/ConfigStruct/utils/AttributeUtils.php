<?php

namespace Endermanbugzjfc\ConfigStruct\utils;

use ReflectionProperty;

final class AttributeUtils
{

    private function __construct()
    {
    }

    public static function trueIfNo(
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