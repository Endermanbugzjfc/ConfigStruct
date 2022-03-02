<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function asort;
use function count;

final class Parse
{
    use StaticClassTrait;

    /**
     * Parse the data of an array. Base on an object's structure, which is property types and attributes provided.
     * @param array $input
     * @param object $object This object will be modified.
     * @param string[]|null $map See {@link Parse::getPropertyNameToKeyNameMap()}. Key = property name. Value = key name.
     * @return ObjectParseOutput $object.
     */
    public static function arrayToObject(
        array  $input,
        object $object,
        ?array $map = null
    ) : ObjectParseOutput
    {
        $reflect = new ReflectionClass(
            $object
        );
        $output = self::reflectionClass(
            $input,
            $reflect,
            $map
        );
        $output->copyToObject(
            $object
        );
        return $output;
    }

    public static function reflectionClass(
        array           $input,
        ReflectionClass $reflect,
        ?array          $map = null
    ) : ObjectParseOutput
    {
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
                $missing[$propertyName] = $property;
                continue;
            }
            $value = $input[$name];
            unset(
                $input[$name]
            );
            $output[$propertyName] = self::property(
                $name,
                $property,
                $value
            );
        }
        return new ObjectParseOutput(
            $reflect,
            $output ?? [],
            $input,
            $missing ?? []
        );
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
            return new ChildStructParseOutput(
                $name,
                $property,
                self::reflectionClass(
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

        return new RawParseOutput(
            $property,
            $name,
            $value
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

    /**
     * @param ReflectionProperty[] $properties
     * @param string[] $map Key = property name. Value = key name.
     * @return ReflectionProperty[] Key = property name.
     */
    protected static function getMissingElements(
        array $properties,
        array $map
    ) : array
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $name = $map[$propertyName] ?? null;
            if ($name === null) {
                $missing[$propertyName] = $property;
            }
        }
        return $missing ?? [];
    }

}