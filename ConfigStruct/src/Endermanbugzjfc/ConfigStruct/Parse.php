<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\struct\StructHolderInterface;
use pocketmine\utils\Config;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

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
     * @throws ReflectionException
     */
    public static function array(
        StructHolderInterface $holder,
        array                 $array,
    ) : object
    {
        $reflect = new ReflectionClass($struct);
        foreach (
            $reflect->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
        }
    }

}