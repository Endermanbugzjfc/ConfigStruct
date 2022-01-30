<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseProperty;
use ReflectionNamedType;
use function class_exists;

class ChildStructParser implements SpecialParserInterface
{

    public function isParserForProperty(
        ParseProperty $property
    ) : bool
    {
        $type = $property->getReflection()->getType();
        if (!$type instanceof ReflectionNamedType) {
            return false;
        }

        if (!class_exists($type->getName())) {
            return false;
        }

        return true;
    }

    public function isParserForValue(
        ParseProperty $property,
        array         $value
    ) : bool
    {
        return $this->isParserForProperty($property);
    }

    public function parseValue(
        ParseProperty $property,
        array         $value
    ) : mixed
    {
        // TODO: Implement parseValue() method.
    }
}