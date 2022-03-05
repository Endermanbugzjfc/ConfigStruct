<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\ParseContext\BasePropertyContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ChildObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ListContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\PropertyDetails;
use Endermanbugzjfc\ConfigStruct\ParseContext\RawContext;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
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
     * @return ObjectContext $object.
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
        $output->copyToObject(
            $object
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
     * @param BasePropertyContext $context Non-abstract property parse contexts will be created from this.
     * @param mixed $value
     * @return BasePropertyContext A non-abstract property parse context.
     */
    public static function property(
        BasePropertyContext $context,
        mixed               $value
    ) : BasePropertyContext
    {
        $property = $context->getReflection();
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
            return ChildObjectContext::create(
                $context,
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
                    $property,
                    $listReflects ?? [],
                    $input
                );
                $elements[$key] = $element;
            }
            return ListContext::create(
                $context,
                $elements ?? [],
                $errs ?? []
            );
        }

        return RawContext::create(
            $context,
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
                    throw new StructureError(
                        "Duplicated key name \"$name\" in $debugClass->$name"
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
     * Find the best matching struct for the input (list element) by checking the count of handled elements. The struct which handles the most elements will be selected. The input will then be parsed into a {@link ObjectContext} with the selected struct.
     *
     * Incompatible structs will never be used.
     * @param ReflectionProperty $property Property is needed for {@link StructureError} message.
     * @param ReflectionClass[] $listTypes Struct candidates.
     * @param array $input An array which was converted from object.
     * @return ObjectContext|null Null = no suitable type for this input (all types are incompatible).
     */
    public static function listElement(
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
            $errs = [];
            $output->copyToNewObject(
                $errs
            );
            if (!empty($errs)) {
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