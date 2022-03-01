<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Closure;
use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function class_exists;
use function is_callable;

final class Parse
{
    use StaticClassTrait;

    /**
     * Copy the data of an array to object. Base on the object's structure, which is property types and attributes provided.
     * @param array $input
     * @param object $object
     * @return StructParseOutput $object.
     */
    public static function arrayInput(
        array  $input,
        object $object
    ) : StructParseOutput
    {
        $reflect = new ReflectionClass(
            $object
        );
        foreach (
            $reflect->getProperties(
                ReflectionProperty::IS_PUBLIC
            ) as $property
        ) {
            $names = array_map(
                fn(ReflectionAttribute $keyName) : string => $keyName
                    ->getArguments()
                [0],
                $property->getAttributes(
                    KeyName::class
                )
            );
            if (empty($names)) {
                $names = [
                    $property->getName()
                ];
            }
            $ok = false;
            foreach ($names as $name) {
                if (array_key_exists(
                    $name,
                    $input
                )) {
                    $ok = true;
                    break;
                }
            }
            if (
                !isset($name)
                or
                !$ok
            ) {
                $missing[] = $property;
                continue;
            }
            $output[$property->getName()] = self::property(
                $name,
                $property,
                $property->getValue(
                    $object
                )
            );
        }
    }

    public static function property(
        string             $name,
        ReflectionProperty $property,
        mixed              $value
    ) : PropertyParseOutput
    {

    }

    /**
     * Look at README.md for example.
     *
     * @param object $object
     * @param array $input
     * @param bool $copyOutputValue
     * @return StructParseOutput
     */
    public static function parseStruct(
        object $object,
        array  $input,
        bool   $copyOutputValue = true
    ) : StructParseOutput
    {
        $output = self::parseStructReflection(
            new ReflectionClass($object),
            $input
        );
        if ($copyOutputValue) {
            $output->copyValuesToObject($object);
        }
        return $output;
    }

    public static function parseStructReflection(
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
            $outputs[$property->getName()] = is_callable($output)
                ? $output()
                : $output;
        }
        foreach (
            array_diff(
                array_keys($input),
                array_values($map)
            ) as $name
        ) {
            $unhandled[$name] = $input[$name];
        }

        return StructParseOutput::create(
            $reflection,
            $outputs ?? [],
            $unhandled ?? [],
            $missing ?? []
        );
    }

    /**
     * @param ReflectionProperty[] $properties
     * @param array $input
     * @return array<string, string>
     */
    protected static function getPropertyNameToKeyNameMap(
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
            ($class = self::getSafeChildStructClass($property)) !== null
        ) {
            return fn() => self::parseChildStruct(
                $property,
                $keyName,
                $value,
                $class
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
        mixed              $value,
        ReflectionClass    $class
    ) : PropertyParseOutput
    {
        return ChildStructParseOutput::create(
            $property,
            $keyName,
            self::parseStructReflection(
                $class,
                $value
            )
        );
    }

    protected static function getSafeChildStructClass(
        ReflectionProperty $property
    ) : ?ReflectionClass
    {
        $class = $property->getType()->getName();
        if ($class === "self") {
            return $property->getDeclaringClass();
        }
        return class_exists($class)
            ? new ReflectionClass($class)
            : null;
    }

}