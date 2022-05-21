<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;
use function get_debug_type;

final class ReflectionUtils
{
    use StaticClassTrait;

    /**
     * @return ReflectionNamedType[]
     */
    public static function getPropertyTypes( // TODO: Unit test.
        ReflectionProperty $property
    ) : array {
        $type = $property->getType();

        if ($type === null) {
            return [];
        }
        if ($type instanceof ReflectionNamedType) {
            return [$type];
        }
        if ($type instanceof ReflectionUnionType) {
            return $type->getTypes();
        }

        $className = $property->getDeclaringClass()->getName();
        $propertyName = $property->getName();
        return throw new RuntimeException(
            "Expecting $className->$propertyName is in the form of null / " . ReflectionNamedType::class . " / " . ReflectionUnionType::class . ", got " . get_debug_type($type)
        );
    }
}