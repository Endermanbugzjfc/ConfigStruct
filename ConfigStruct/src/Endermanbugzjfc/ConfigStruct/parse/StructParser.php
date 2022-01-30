<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_values;
use function class_exists;

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
            $key = $map[$property->getName()] ?? $property->getName();
            if (!array_key_exists(
                $key,
                $input
            )) {
                $missing[$property->getName()] = $property;
                continue;
            }
            self::parseProperty($property, $key, $input[$key]);
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

    /**
     * @param ReflectionProperty[] $properties
     * @param array $input
     * @return array
     */
    public static function getPropertyNameToKeyNameMap(
        array $properties,
        array $input
    ) : array
    {
        foreach ($properties as $property) {
            foreach (
                $property->getAttributes(KeyName::class)
                as $keyName
            ) {
                $name = $keyName->getArguments()[0];
                if (!array_key_exists($name, $input)) {
                    continue;
                }
                $names[$property->getName()] = $name;
                break;
            }
        }
        return $names ?? [];
    }

    public static function parseProperty(
        ReflectionProperty $property,
        string             $keyName,
        mixed              $value
    ) : PropertyParseOutput
    {
        $type = $property->getType();
        if (
            $type instanceof ReflectionNamedType
            and
            class_exists($type->getName())
        ) {
            return ChildStructParseOutput::indicator(
                $property
            );
        }

        // TODO: array

        return MixedParseOutput::create(
            $property,
            $keyName,
            $value
        );
    }

}