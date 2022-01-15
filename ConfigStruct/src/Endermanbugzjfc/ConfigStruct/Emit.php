<?php

namespace Endermanbugzjfc\ConfigStruct;

use pocketmine\utils\Config;
use ReflectionClass;
use ReflectionProperty;

final class Emit
{

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. Values of its initiated properties, or uninitiated struct class properties with an {@link AutoInitializeChildStruct} attribute will be encoded recursively in the given type (language) and file path.
     * @param string $file Absolute or related path of the file that the encoded content will be saved to.
     * @param int $type The type (language) of the file (see the constants in {@link Config} class).
     * @return void False = invalid path (file or folder doesn't exists).
     */
    public static function file(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : void
    {
        @mkdir(dirname($file));
        $config = new Config($file, $type);
        foreach (self::array($struct) as $k => $v) {
            $config->setNested($k, $v);
        }
    }

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. Values of its initiated properties, or uninitiated struct class properties with an {@link AutoInitializeChildStruct} attribute will be encoded recursively in the given type (language) in the form of nested scalar keys-values array and be returned.
     * @return array Return a nested scalar keys-values array which holds the encoded content.
     */
    public static function array(object $struct) : array
    {
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
        }
        return $array ?? [];
    }

}