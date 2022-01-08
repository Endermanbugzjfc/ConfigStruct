<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;


use Attribute;
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
        $default = $property->getAttributes(AutoInitializeChildStruct::class)[0] ?? null;
        if (!isset($default)) {
            return false;
        }
        if (!isset($default->getArguments()[0])) {
            if ($property->getType() instanceof ReflectionNamedType) {
                $class = $property->getName();
            }
        } else {
            $class = $default->getArguments()[0];
        }
        if (isset($class) and class_exists($class)) {
            $value = new $class;
            return true;
        }
        return false;
    }

}