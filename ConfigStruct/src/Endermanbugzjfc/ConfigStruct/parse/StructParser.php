<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionClass;
use ReflectionProperty;
use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_values;

final class StructParser
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    public static function parseStruct(
        ReflectionClass $reflection,
        array           $input
    ) : StructParseOutput
    {
        $properties = $reflection->getProperties(
            ReflectionProperty::IS_PUBLIC
        );
        $map = self::getPropertyNameToKeyNameMap(
            $properties,
            $input
        );

        foreach ($properties as $property) {
            $key = $map[$property->getName()];
            if (!array_key_exists(
                $key,
                $input
            )) {
                $missing[$property->getName()] = $property;
                continue;
            }
            self::parseProperty($property, $key);
        }

        return StructParseOutput::create(
            $reflection,
            [],
            array_diff(
                array_keys($input),
                array_values($map)
            ),
            $missing ?? []
        );
    }

    public static function getPropertyNameToKeyNameMap(
        array $properties,
        array $input
    ) : array
    {

    }

    public static function parseProperty(
        ReflectionProperty $property,
        mixed              $key
    ) : PropertyParseOutput
    {

    }

}