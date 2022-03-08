<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\ParseContext\BasePropertyContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ChildObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ListContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\PropertyDetails;
use Endermanbugzjfc\ConfigStruct\ParseContext\RawContext;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
use Endermanbugzjfc\ConfigStruct\Utils\StructureErrorThrowerTrait;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_key_exists;
use function implode;
use function in_array;

final class Parse
{
    use StaticClassTrait, StructureErrorThrowerTrait;

    /**
     * Parse the data of an array. Base on an object's structure, which is property types and attributes provided.
     * @param array $input
     * @param object $object The parsed data will not be automatically copied to the object, please use {@link ObjectContext::copyToObject()}.
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
        return self::objectByReflection(
            $input,
            $reflect,
            $map
        );
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
     *
     * If the type of a property is "self", the declaring class of that property will be used. As long as the child class (who extends the declaring class) does not override the property.
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
            $raw = $type->getName();
            if ($raw === "self") {
                $raw = $details->getReflection()->getDeclaringClass()->getName();
            }
            try {
                $reflect = new ReflectionClass(
                    $raw
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
        if ($listTypes !== []) {
            foreach ($listTypes as $listType) {
                try {
                    $listTypeRaw = $listType->getArguments()[0];
                    $listReflect = new ReflectionClass(
                        $listTypeRaw
                    );
                } catch (ReflectionException $err) {
                    self::invalidStructure(
                        new StructureError(
                            "List type attribute has invalid class",
                            $err
                        ),
                        $property
                    );
                }
                $listReflects[] = $listReflect;
            }
            foreach ($value as $key => $input) {
                $element = self::findMatchingStruct(
                    $property,
                    $listReflects ?? [],
                    $input
                );
                if ($element instanceof ParseError) {
                    $errs[$key] = $element->getErrorsTree();
                    continue;
                }

                unset(
                    $value[$key]
                );
                $elements[$key] = $element;
            }
            return new ListContext(
                $details,
                $elements ?? [],
                $errs ?? [],
                $value
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

            $names = $duplicated = [];
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
                    $duplicated[] = $name;
                }
                $names[] = $name;
            }

            if ($duplicated !== []) {
                $debugClass = $property->getDeclaringClass()->getName();
                $duplicatedList = implode(
                    "\", \"",
                    $duplicated
                );
                throw new StructureError(
                    "Duplicated key name \"$duplicatedList\" in $debugClass->$propertyName"
                );
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
     * Find the struct with the most handled elements count. And parse the input with the selected struct.
     *
     * @param ReflectionProperty $property Property is needed for {@link StructureError} message.
     * @param ReflectionClass[] $candidates Struct candidates.
     * @param array $input An array which was converted from object.
     * @return ObjectContext|ParseError If all structs conflict with the input, the error of the first {@link ObjectContext} will be returned.
     */
    public static function findMatchingStruct(
        ReflectionProperty $property,
        array              $candidates,
        array              $input
    ) : ObjectContext|ParseError
    {
        if ($candidates === []) {
            throw new InvalidArgumentException(
                "No struct candidates were given"
            );
        }

        $listTypesRaw = [];
        $firstErr = null;
        foreach ($candidates as $key => $listType) {
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
            } catch (ParseError $err) {
                $firstErr ??= $err;
                continue;
            }
            $outputs[$key] = $output;
        }
        if (!isset($outputs)) {
            return $firstErr;
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