<?php

namespace Endermanbugzjfc\ConfigStruct;

use ReflectionClass;
use ReflectionProperty;

final class Emit
{

    // TODO: __construct()

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. Values of its initiated properties, or uninitiated struct class properties with an {@link AutoInitializeChildStruct} attribute will be encoded recursively in the given type (language) in the form of nested scalar keys-values array and be returned.
     * @return array Return a nested scalar keys-values array which holds the encoded content.
     */
    public static function array(object $struct) : array
    {
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
        }
        return $array ?? [];
    }

}