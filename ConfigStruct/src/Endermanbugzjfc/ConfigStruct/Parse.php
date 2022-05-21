<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\ParseContext\BasePropertyContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ChildObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ListContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseContext\PropertyDetails;
use Endermanbugzjfc\ConfigStruct\ParseContext\RawContext;
use Endermanbugzjfc\ConfigStruct\ParseError\TypeMismatchError;
use Endermanbugzjfc\ConfigStruct\Utils\ReflectionUtils;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
use Endermanbugzjfc\ConfigStruct\Utils\StructureErrorThrowerTrait;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use TypeError;
use function array_key_exists;
use function array_unique;
use function assert;
use function class_exists;
use function count;
use function get_debug_type;
use function implode;
use function in_array;
use function is_array;

final class Parse
{
    use StaticClassTrait, StructureErrorThrowerTrait;

    /**
     * @template T of object
     * Parse the data of an array. Base on an object's structure, which is property types and attributes provided.
     * @param T $object The parsed data will not be automatically copied to the object, please use {@link ObjectContext::copyToObject()}.
     * @param mixed[] $input
     * @return ObjectContext<T> $object.
     */
    public static function object(
        object $object,
        array  $input
    ) : ObjectContext {
        $reflect = new ReflectionClass(
            $object
        );
        return self::objectByReflection(
            $reflect,
            $input
        );
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $reflect
     * @param mixed[] $input
     * @return ObjectContext<T>
     */
    public static function objectByReflection(
        ReflectionClass $reflect,
        array           $input
    ) : ObjectContext {
        $properties = $reflect->getProperties(
            ReflectionProperty::IS_PUBLIC
        );
        try {
            $map = self::getPropertyNameToKeyNameMap(
                $properties,
                $input
            );
        } catch (Exception $err) {
            self::invalidStructure( // Duplicated key names.
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
        string $name,
        ReflectionProperty $property
    ) : PropertyDetails {
        return new PropertyDetails(
            $name,
            $property
        );
    }

    /**
     * Redirect to the correct parse function. Base on the property's type and attributes provided.
     *
     * Property type "self" will be interpreted as the declaring class of that property. Overriding of "self" will be implemented in future versions.
     * @return BasePropertyContext A non-abstract property parse context.
     */
    public static function property(
        PropertyDetails $details,
        mixed           $value
    ) : BasePropertyContext {
        if (is_array(
            $value
        )) {
            $property = $details->getReflection();
            $types = ReflectionUtils::getPropertyTypes($property);
            $candidates = $raws = [];
            foreach ($types as $type) {
                $raw = $type->getName();
                if ($raw === "self") {
                    $raw = $details->getReflection()->getDeclaringClass()->getName();
                } elseif (!class_exists($raw)) {
                    continue;
                }
                $raws[] = $raw;
            }
            $raws = array_unique(
                $raws
            ); // Since it is possible to have both "self" and the own class name in an union-types.
            /**
             * @var class-string[] $raws
             */
            foreach ($raws as $raw) {
                $candidate = new ReflectionClass(
                        $raw
                    );
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
                    $listTypeRaw = $listType->getArguments()[0];
                    try {
                        $listReflect = new ReflectionClass(
                            $listTypeRaw
                        );
                    } catch (ReflectionException $err) {
                        self::invalidStructure( // List type attribute has invalid class.
                            new StructureError(
                                "List type attribute has invalid class",
                                $err
                            ),
                            $property
                        );

                        throw new AssertionError("unreachable");
                    }
                    $listReflects[] = $listReflect;
                }
                foreach ($value as $key => $input) {
                    try {
                        $element = self::findMatchingStruct(
                            $listReflects,
                            $input
                        );
                    } catch (Exception $err) {
                        self::invalidStructure( // Duplicated struct candidates.
                            $err,
                            $property
                        );

                        throw new AssertionError("unreachable");
                    } catch (TypeError $err) { // @phpstan-ignore-line $input might not be an array.
                        $elementsErrorsTree[$key] = new TypeMismatchError(
                            $err,
                            [
                                "array"
                            ],
                            get_debug_type($input)
                        );
                        continue;
                    }
                    if ($element instanceof ParseErrorsWrapper) {
                        $elementsErrorsTree[$key] = $element->getErrorsTree();
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
                    $elementsErrorsTree ?? [],
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
     * @param mixed[] $input
     * @return array<string, string>
     * @throws Exception Duplicated key names.
     */
    protected static function getPropertyNameToKeyNameMap(
        array $properties,
        array $input
    ) : array {
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
                    $names, true
                    // PHP array index is not strictly typed. Classic PHP.
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
     * @template T of object
     * @param ReflectionClass<T>[] $candidates Struct candidates.
     * @param mixed[] $input An array which was converted from object.
     * @return ObjectContext<T>|ParseErrorsWrapper If all structs conflict with the input, the error of the first {@link ObjectContext} will be returned.
     * @throws Exception Duplicated struct candidates.
     */
    public static function findMatchingStruct(
        array $candidates,
        array $input
    ) : ObjectContext|ParseErrorsWrapper {
        if ($candidates === []) {
            throw new InvalidArgumentException(
                "No struct candidates were given"
            );
        }

        $raws = $duplicated = $outputs = [];
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
            $raws[] = $raw;
            $output = self::objectByReflection(
                $candidate,
                $input
            );
            try {
                $output->copyToNewObject(
                    "object"
                );
            } catch (ParseErrorsWrapper $err) {
                $firstErr = $err;
                continue;
            }
            $outputs[] = $output;
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
        if ($outputs === []) {
            assert(isset($firstErr));
            return $firstErr;
        }
        $leastUnhandled = $outputs[0];
        foreach ($outputs as $output2) {
            if (
                count(
                    $leastUnhandled->getUnhandledElements()
                ) < count(
                    $output2->getUnhandledElements()
                )
            ) {
                continue;
            }
            $leastUnhandled = $output2;
        }

        return $leastUnhandled;
    }
}