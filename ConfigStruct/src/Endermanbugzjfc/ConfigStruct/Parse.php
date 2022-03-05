<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\ParseContext\BasePropertyContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ChildObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ListContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\PropertyDetails;
use Endermanbugzjfc\ConfigStruct\ParseContext\RawContext;
use Endermanbugzjfc\ConfigStruct\ParseError\InvalidListTypeError;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_count_values;
use function array_key_exists;
use function array_map;
use function implode;
use function in_array;

final class Parse
{
    use StaticClassTrait;

    /**
     * Parse the data of an array. Base on an object's structure, which is property types and attributes provided.
     * @param array $input
     * @param object $object The parsed data will not be automatically copied to the object, please use {@link ObjectContext::copyToObject()}.
     * @param string[]|null $map See {@link Parse::getPropertyNameToKeyNameMap()}. Key = property name. Value = key name.
     * @return ObjectContext $object.
     * @throws ParseError
     */
    public static function object(
        array  $input,
        object $object,
        ?array $map = null
    ) : ObjectContext
    {
        $reflect = new ReflectionClass(
            $object
        );
        $output = self::objectByReflection(
            $input,
            $reflect,
            $map
        );
        return $output;
    }

    public static function objectByReflection(
        array           $input,
        ReflectionClass $reflect,
        ?array          $map = null
    ) : ObjectContext
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
            $context = self::createPropertyDetails(
                $name,
                $property
            );
            $output[$propertyName] = self::property(
                $context,
                $value
            );
        }
        return new ObjectContext(
            $reflect,
            $output ?? [],
            $input,
            $missing ?? []
        );
    }

    protected static function createPropertyDetails(
        mixed              $name,
        ReflectionProperty $property
    ) : PropertyDetails
    {
        return new PropertyDetails(
            $name,
            $property
        );
    }

    /**
     * Redirect to the correct parse function. Base on the property's type and attributes provided.
     * @param PropertyDetails $details
     * @param mixed $value
     * @return BasePropertyContext A non-abstract property parse context.
     */
    public static function property(
        PropertyDetails $details,
        mixed           $value
    ) : BasePropertyContext
    {
        $property = $details->getReflection();
        $type = $property->getType();
        if ($type instanceof ReflectionNamedType) {
            try {
                $reflect = new ReflectionClass(
                    $type->getName()
                );
            } catch (ReflectionException) {
            }
        }
        if (isset($reflect)) {
            return new ChildObjectContext(
                $details,
                self::objectByReflection(
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
                    $listTypeRaw = $listType->getArguments()[0];
                    $listReflect = new ReflectionClass(
                        $listTypeRaw
                    );
                } catch (ReflectionException $err) {
                    $errs[] = new InvalidListTypeError(
                        $err,
                        $listTypeRaw
                    );
                    continue;
                }
                $listReflects[] = $listReflect;
            }
            foreach ($value as $key => $input) {
                $element = self::listElement(
                    $property,
                    $listReflects ?? [],
                    $input
                );
                if ($element === null) {
                    continue; // TODO: Find better solution.
                }
                $elements[$key] = $element;
            }
            return new ListContext(
                $details,
                $elements ?? [],
                $errs ?? []
            );
        }

        return new RawContext(
            $details,
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
            $propertyName = $property->getName();
            foreach (
                $property->getAttributes(KeyName::class)
                as $keyName
            ) {
                $names[] = $keyName->getArguments()[0];
            }
            if (!empty($names)) {
                $namesCount = array_count_values($names);
                foreach ($namesCount as $name => $count) {
                    if ($count > 1) {
                        continue;
                    }
                    $duplicated[] = $name;
                }
                if (!empty($duplicated)) {
                    $debugClass = $property->getDeclaringClass()->getName();
                    $duplicatedQuoted = array_map(
                        fn(string $name) : string => "\"$name\"",
                        $duplicated
                    );
                    $duplicatedList = implode(
                        ", ",
                        $duplicatedQuoted
                    );
                    throw new StructureError(
                        "Duplicated key name $duplicatedList in $debugClass->$propertyName"
                    );
                }
            }

            foreach ($names ?? [
                $propertyName
            ] as $name) {
                if (!array_key_exists($name, $input)) {
                    continue;
                }
                $map[$propertyName] = $name;
                break;
            }
        }
        return $map ?? [];
    }

    /**
     * Find the best matching struct for the input (list element) by checking the count of handled elements. The struct which handles the most elements will be selected. The input will then be parsed into a {@link ObjectContext} with the selected struct.
     *
     * Incompatible structs will never be used.
     * @param ReflectionProperty $property Property is needed for {@link StructureError} message.
     * @param ReflectionClass[] $listTypes Struct candidates.
     * @param array $input An array which was converted from object.
     * @return ObjectContext|null Null = no suitable type for this input (all types are incompatible).
     */
    public
    static function listElement(
        ReflectionProperty $property,
        array              $listTypes,
        array              $input
    ) : ?ObjectContext
    {
        $listTypesRaw = [];
        foreach ($listTypes as $key => $listType) {
            $listTypeRaw = $listType->getName();
            if (in_array(
                $listTypeRaw,
                $listTypesRaw,
                true
            )) {
                $debugClass = $property->getDeclaringClass()->getName();
                $debugProperty = $property->getName();
                throw new StructureError(
                    "Duplicated list type $listTypeRaw in $debugClass->$debugProperty"
                );
            }
            $output = self::objectByReflection(
                $input,
                $listType
            );
            try {
                $output->copyToNewObject(
                    "object"
                );
            } catch (ParseError) {
                continue;
            }
            $outputs[$key] = $output;
        }
        if (!isset($outputs)) {
            return null;
        }
        $leastUnhandled = null;
        foreach ($outputs as $output2) {
            if ($leastUnhandled instanceof ObjectContext) {
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