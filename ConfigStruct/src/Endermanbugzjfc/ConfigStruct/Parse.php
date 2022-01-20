<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\struct\StructHolderInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_key_exists;
use function class_exists;

final class Parse
{


    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    /**
     * Look at README.md for examples.
     *
     * @param StructHolderInterface $holder Parsed content will be copied to the struct given by this holder.
     * @param array<bool|int|float|string, bool|int|float|string|array> $array The input to be parsed, nested scalar keys-values array.
     * @return object The struct which contains the parsed data.
     */
    public static function array(
        StructHolderInterface $holder,
        array                 $array,
    ) : object
    {
        $struct = $holder->newStructForParsing();
        $array = self::mapKeyNamesToPropertyNames($struct, $array);

        $class = new ReflectionClass($struct);
        foreach (
            $class->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            if (!array_key_exists($property->getName(), $array)) {
                continue;
            }
            $value = self::field($property, $array[$property->getName()]);
            if ($property->isInitialized()) {
                $default = $property->getValue($struct);
            }
            if (isset($default) and $value === $default) {
                continue;
            }
            $property->setValue($struct, $value);
        }
        return $struct;
    }

    /**
     * @param ReflectionProperty $property Property is needed to convert parsed content base on its structure.
     * @param array<bool|int|float|string, bool|int|float|string|array> $field The input to be parsed, nested scalar keys-values array.
     * @return mixed Field parse output.
     * @throws ReflectionException
     */
    public static function field(
        ReflectionProperty $property,
        array              $field
    ) : mixed
    {
        $group = $property->getAttributes(Group::class)[0] ?? null;
        if ($group !== null) {
            return self::groupField($group, $field);
        }

        if (
            $property->getType() instanceof ReflectionNamedType
            and
            class_exists($child = $property->getType()->getName())
        ) {
            return self::childStructField(
                new ReflectionClass($child),
                $field
            );
        }
        return $field;
    }

    /**
     * @param ReflectionClass $class Reflection of the child struct class.
     * @param array<bool|int|float|string, bool|int|float|string|array> $field The input to be parsed, nested scalar keys-values array.
     * @return object Child struct which contains the parsed data.
     */
    public static function childStructField(
        ReflectionClass $class,
        array           $field
    ) : object
    {
    }

    /**
     * @param object $struct Struct is needed to know the properties it have.
     * @param array<bool|int|float|string, bool|int|float|string|array> $array Key names will be mapped to property names. Unused fields will be filtered out.
     * @return array<bool|int|float|string, bool|int|float|string|array> Please aware that the output could have missing fields.
     */
    public static function mapKeyNamesToPropertyNames(
        object $struct,
        array  $array
    ) : array
    {
    }

    /**
     * @param ReflectionAttribute $group A {@link Group} attribute instance.
     * @param array<bool|int|float|string, bool|int|float|string|array> $field The input to be converted.
     * @return array object[], array dimension is based on the "wrapping" parameter in {@link Group::__construct()}.
     */
    public static function groupField(
        ReflectionAttribute $group,
        array               $field
    ) : array
    {
    }

    /**
     * @param ReflectionAttribute $recursive A {@link Recursive} attribute instance.
     * @param array<bool|int|float|string, bool|int|float|string|array> $field The input to be converted.
     * @return object Recursive child struct.
     */
    public static function recursiveChildStruct(
        ReflectionAttribute $recursive,
        array               $field
    ) : object
    {

    }

}