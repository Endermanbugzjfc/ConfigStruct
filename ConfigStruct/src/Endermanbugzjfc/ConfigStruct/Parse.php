<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\struct\StructHolderInterface;
use pocketmine\utils\Config;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use function array_key_exists;

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
     * @param object $struct An instance of your config struct class. After the file has been parsed, its content will be copied to this instance of on its structure. Then, if there is a missing field, but its property has an {@link AutoInitializeChildStruct} attribute, that property will be initiated with its child struct recursively.
     * @param string $file Absolute or related path of the file that will be read and parsed.
     * @param int $type The type (language) of the file (see the constants in {@link Config} class).
     * @return bool False = invalid path (file or folder doesn't exists).
     */
    public static function file(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : bool
    {
        if (!file_exists($file)) {
            return false;
        }
        self::array(
            $struct,
            (new Config($file, $type))->getAll(),
        );
        return true;
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

        $recursive = $property->getAttributes(Recursive::class)[0] ?? null;
        if ($recursive !== null) {
            return self::recursiveField($recursive, $field);
        }
        return $field;
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
    public static function recursiveField(
        ReflectionAttribute $recursive,
        array               $field
    ) : object
    {

    }

}