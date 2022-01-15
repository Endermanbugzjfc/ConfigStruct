<?php

namespace Endermanbugzjfc\ConfigStruct;

use pocketmine\utils\Config;
use ReflectionClass;
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
     * @param object $struct The instance should be already initialized (from {@link Analyse::initializeStruct()}, you may create cache if this struct used for more than one times). Parsed content will be copied to this instance depending on its structure.
     * @param array<bool|int|float|string, bool|int|float|string|array> $array Config content in the form of nested scalar keys-values array.
     */
    public static function array(
        object $struct,
        array  $array,
    ) : void
    {
        $reflect = new ReflectionClass($struct);
        foreach (
            $reflect->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
        }
    }

}