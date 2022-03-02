<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\StructureError;
use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_key_exists;
use function in_array;

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
                [],
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
                } catch (ReflectionException $err) {
                    $errs[] = $err;
                    continue;
                }
                $listReflects[] = $listReflect;
            }
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
                $errs ?? [],
                $elements ?? []
            );
        }

        return new RawParseOutput(
            $property,
            $name,
            [],
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
            $names = [];
            foreach (
                $property->getAttributes(KeyName::class)
                as $keyName
            ) {
                $name = $keyName->getArguments()[0];
                if (in_array(
                    $name,
                    $names,
                    true
                )) {
                    $debugClass = $property->getDeclaringClass()->getName();
                    $debugProperty = $property->getName();
                    throw new StructureError(
                        "Duplicated key name \"$name\" in $debugClass->$debugProperty"
                    );
                }
                $names[] = $name;
                if (!array_key_exists($name, $input)) {
                    continue;
                }
                $map[$property->getName()] = $name;
                break;
            }
        }
        return $map ?? [];
    }

    /**
     * Find the best matching struct for the input (list element) by checking the count of handled elements. The struct which handles the most elements will be selected. The input will then be parsed into a {@link ObjectParseOutput} with the selected struct.
     *
     * Incompatible structs will never be used.
     * @param ReflectionClass[] $listTypes Struct candidates.
     * @param array $input An array which was converted from object.
     * @return ObjectParseOutput|null Null = no suitable type for this input (all types are incompatible).
     * @throws StructureError Failed to construct a new instance (probably incompatible arguments).
     */
    public static function listElement(
        array $listTypes,
        array $input
    ) : ?ObjectParseOutput
    {
        foreach ($listTypes as $key => $listType) {
            $output = self::reflectionClass(
                $input,
                $listType
            );
            if (!empty($output->getErrors())) {
                continue;
            }
            $outputs[$key] = $output;
        }
        if (!isset($outputs)) {
            return null;
        }
        $leastUnhandled = null;
        foreach ($outputs as $output2) {
            if ($leastUnhandled instanceof ObjectParseOutput) {
                $unhandled = $output2->getUnhandledElements();
                if (
                    $leastUnhandled->getUnhandledElements() < $unhandled
                ) {
                    continue;
                }
                $leastUnhandled = $output2;
            }
        }

        return $leastUnhandled;
    }

}