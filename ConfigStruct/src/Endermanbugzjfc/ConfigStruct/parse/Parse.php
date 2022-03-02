<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
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
                try {
                    $listReflect = new ReflectionClass(
                        $listType->getArguments()[0]
                    );
                } catch (ReflectionException) {
                    continue;
                }
                $listReflects[] = $listReflect;
            }
            // TODO: Handle invalid types.
            foreach ($value as $key => $input) {
                $element = self::listElement(
                    $listReflects ?? [],
                    $input
                );
                $elements[$key] = $element;
            }
            return new ListParseOutput(
                $name,
                $property,
                $elements ?? []
            );
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
     * Find the best matching type for the input (list element) and parse the input into a {@link ObjectParseOutput}. Incompatible types will never be used. During this process, int might be casted to float.
     * @param ReflectionClass[] $listTypes
     * @param array $input An array which was converted from object.
     * @return ObjectParseOutput|null Null = no suitable type for this input (all types are incompatible).
     */
    public static function listElement(
        array $listTypes,
        array $input
    ) : ?ObjectParseOutput
    {
        foreach ($listTypes as $key => $listType) {
            $properties = $listType->getProperties(
                ReflectionProperty::IS_PUBLIC
            );
            $map = self::getPropertyNameToKeyNameMap(
                $properties,
                $input
            );
            $compatible = true;
            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $name = $map[$propertyName] ?? null;
                if ($name === null) {
                    $missing[$propertyName] = $property;
                    continue;
                }

                $compatible = self::isValueCompatibleWithProperty(
                    $property,
                    $input[$name]
                );
                if (!$compatible) {
                    break;
                }
            }
            if ($compatible) {
                $missingCounts[$key] = count($missing ?? []);
            }
        }
        if (empty(
            $missingCounts ?? []
        )) {
            throw new InvalidArgumentException(
                "No list types were given"
            );
        }
        asort($missingCounts);
        $indexes = array_keys($missingCounts);
        $first = $indexes[0];
        $type = $listTypes[$first];

        return self::reflectionClass(
            $input,
            $type
        );
    }

    protected static function isValueCompatibleWithProperty(
        ReflectionProperty $property,
        mixed              $value
    ) : bool
    {
    }

}