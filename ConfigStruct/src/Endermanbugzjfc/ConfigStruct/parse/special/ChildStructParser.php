<?php

namespace Endermanbugzjfc\ConfigStruct\parse\special;

use Endermanbugzjfc\ConfigStruct\parse\ParseTimeProperty;
use ReflectionNamedType;
use function class_exists;

class ChildStructParser implements SpecialParserInterface
{

    public function isParserForProperty(
        ParseTimeProperty $property
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
        ParseTimeProperty $property,
        array             $value
    ) : bool
    {
        return $this->isParserForProperty($property);
    }

    public function parseValue(
        ParseTimeProperty $property,
        array             $value
    ) : mixed
    {
        // TODO: Implement parseValue() method.
    }
}