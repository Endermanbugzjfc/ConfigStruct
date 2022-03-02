<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use AssertionError;
use Closure;
use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_diff;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_values;
use function asort;
use function class_exists;
use function count;
use function is_callable;

final class Parse
{
    use StaticClassTrait;

    /**
     * Copy the data of an array to object. Base on the object's structure, which is property types and attributes provided.
     * @param array $input
     * @param object $object
     * @param string[]|null $map See {@link Parse::getPropertyNameToKeyNameMap()}. Key = property name. Value = key name.
     * @return StructParseOutput $object.
     */
    public static function arrayInput(
        array  $input,
        object $object,
        ?array $map = null
    ) : StructParseOutput
    {
        $reflect = new ReflectionClass(
            $object
        );
        $properties = $reflect->getProperties(
            ReflectionProperty::IS_PUBLIC
        );
        $map ??= self::getPropertyNameToKeyNameMap(
            $properties,
            $input
        );
        foreach (
            $properties as $property
        ) {
            $propertyName = $property->getName();
            $name = $map[$propertyName] ?? null;
            if ($name === null) {
                $missing[] = $name;
                continue;
            }
            $output[$propertyName] = self::property(
                $name,
                $property,
                $property->getValue(
                    $object
                )
            );
        }
    }

    /**
     * Redirect to the correct parse function. Base on the property's type and attributes provided.
     * @param string $name
     * @param ReflectionProperty $property
     * @param mixed $value
     * @return PropertyParseOutput
     */
    public static function property(
        string             $name,
        ReflectionProperty $property,
        mixed              $value
    ) : PropertyParseOutput
    {
        try {
            $type = $property->getType();
            if ($type instanceof ReflectionNamedType) {
                $reflect = new ReflectionClass(
                    $type->getName()
                );
            }
        } catch (ReflectionException) {
        }
        if (isset($reflect)) {
            // TODO: Recode
            return ChildStructParseOutput::create(
                $property,
                $name,
                self::arrayInput(
                    $value,
                    $reflect
                )
            );
        }

        $listTypes = $property->getAttributes(
            ListType::class
        );
        if (!empty($listTypes)) {
            foreach ($listTypes as $listType) {
                $type = $listType->getArguments()[0];
                if (isset(
                    $missingCounts[$type]
                )) {
                    continue;
                }
                try {
                    $reflect = new ReflectionClass(
                        $type
                    );
                } catch (ReflectionException) {
                    // TODO
                    continue;
                }
                $properties = $reflect->getProperties(
                    ReflectionProperty::IS_PUBLIC
                );
                $map = self::getPropertyNameToKeyNameMap(
                    $properties,
                    $value
                );
                $missing = array_filter(
                    $map,
                    fn(
                        $k,
                        $v
                    ) : bool => $v === null
                );
                $missingCounts[$type] = count($missing);
            }
            asort($missing);
            $sortTypes = array_keys($missing);
            $sortType = $sortTypes[0];
            throw new AssertionError($sortType); // TODO
        }

        return RawParseOutput::create(
            $property,
            $name,
            $value
        );
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