<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Closure;
use Endermanbugzjfc\ConfigStruct\KeyName;
use PhpParser\Error;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_values;
use function class_exists;
use function is_callable;

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
            $output = self::parseProperty($property, $key, $input[$key]);
            if (is_callable($output)) {
                $output();
                continue;
            }
            $outputs[] = $output;
        }

        return StructParseOutput::create(
            $reflection,
            $outputs ?? [],
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
    ) : PropertyParseOutput|Closure
    {
        $type = $property->getType();
        if (
            $type instanceof ReflectionNamedType
            and
            class_exists($type->getName())
        ) {
            return fn() => self::parseChildStruct(
                $property,
                $keyName,
                $value
            );
        }

        // TODO: array

        return RawParseOutput::create(
            $property,
            $keyName,
            $value
        );
    }

    public static function parseChildStruct(
        ReflectionProperty $property,
        string             $keyName,
        mixed              $value
    ) : PropertyParseOutput
    {
        try {
            $output = self::parseStruct(
                new ReflectionClass($property->getType()->getName()),
                $value
            );
        } catch (ReflectionException $err) {
            throw new Error($err);
        }
        return ChildStructParseOutput::create(
            $property,
            $keyName,
            $output
        );
    }

}