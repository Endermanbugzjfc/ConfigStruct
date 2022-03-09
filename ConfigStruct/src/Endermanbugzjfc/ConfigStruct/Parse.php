<?php

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\ParseContext\BasePropertyContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ChildObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ListContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\PropertyDetails;
use Endermanbugzjfc\ConfigStruct\ParseContext\RawContext;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
use Endermanbugzjfc\ConfigStruct\Utils\StructureErrorThrowerTrait;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_key_exists;
use function array_unique;
use function implode;
use function in_array;
use function is_array;

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
        try {
            $map ??= self::getPropertyNameToKeyNameMap(
                $properties,
                $input
            );
        } catch (Exception $err) {
            self::invalidStructure(
                $err,
                $reflect
            );
        }
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
     * Property type "self" will be interpreted as the declaring class of that property. Overriding of "self" will be implemented in future versions.
     * @param PropertyDetails $details
     * @param mixed $value
     * @return BasePropertyContext A non-abstract property parse context.
     */
    public static function property(
        PropertyDetails $details,
        mixed           $value
    ) : BasePropertyContext
    {
        if (is_array(
            $value
        )) {
            $property = $details->getReflection();
            $types = $property->getType();
            $types = $types === null
                ? []
                : (
                $types instanceof ReflectionNamedType
                    ? [$types]
                    : $types->getTypes()
                );
            $candidates = $raws = [];
            foreach ($types as $type) {
                $raw = $type->getName();
                if ($raw === "self") {
                    $raw = $details->getReflection()->getDeclaringClass()->getName();
                }
                $raws[] = $raw;
            }
            $raws = array_unique(
                $raws
            ); // Since it is possible to have both "self" and the own class name in an union-types.
            foreach ($raws as $raw) {
                try {
                    $candidate = new ReflectionClass(
                        $raw
                    );
                } catch (ReflectionException) {
                    continue;
                }
                $candidates[] = $candidate;
            }
            if ($candidates !== []) {
                try {
                    $found = self::findMatchingStruct(
                        $candidates,
                        $value
                    );
                } catch (Exception $err) {
                    throw new AssertionError(
                        "unreachable",
                        -1,
                        $err
                    );
                }
                return new ChildObjectContext(
                    $details,
                    $found
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
                    try {
                        $element = self::findMatchingStruct(
                            $listReflects ?? [],
                            $input
                        );
                    } catch (Exception $err) {
                        self::invalidStructure(
                            $err,
                            $property
                        );
                    }
                    if ($element instanceof ParseErrorsWrapper) {
                        $tree[$key] = $element->getErrorsTree();
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
                    $tree ?? [],
                    $value
                );
            }

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
     * @throws Exception Duplicated key names.
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
                $duplicatedList = implode(
                    "\", \"",
                    $duplicated
                );
                throw new Exception(
                    "Duplicated key names \"$duplicatedList\""
                );
            }
            foreach (
                $names === []
                    ? [$propertyName]
                    : $names
                as $name
            ) {
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
     * @param ReflectionClass[] $candidates Struct candidates.
     * @param array $input An array which was converted from object.
     * @return ObjectContext|ParseErrorsWrapper If all structs conflict with the input, the error of the first {@link ObjectContext} will be returned.
     * @throws Exception Duplicated struct candidates.
     */
    public static function findMatchingStruct(
        array $candidates,
        array $input
    ) : ObjectContext|ParseErrorsWrapper
    {
        if ($candidates === []) {
            throw new InvalidArgumentException(
                "No struct candidates were given"
            );
        }

        $raws = $duplicated = [];
        $firstErr = null;
        foreach ($candidates as $key => $candidate) {
            if ($candidate->isAbstract()) {
                continue;
            }
            $raw = $candidate->getName();
            if (in_array(
                $raw,
                $raws,
                true
            )) {
                $duplicated[] = $raw;
                continue;
            }
            $output = self::objectByReflection(
                $input,
                $candidate
            );
            try {
                $output->copyToNewObject(
                    "object"
                );
            } catch (ParseErrorsWrapper $err) {
                $firstErr ??= $err;
                continue;
            }
            $outputs[$key] = $output;
        }
        if ($duplicated !== []) {
            $duplicatedList = implode(
                ", ",
                $duplicated
            );
            throw new Exception(
                "Duplicated struct candidates $duplicatedList"
            );
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