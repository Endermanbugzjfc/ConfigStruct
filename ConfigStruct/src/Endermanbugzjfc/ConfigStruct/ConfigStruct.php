<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\AutoInitializeChildStruct;
use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Required;
use Endermanbugzjfc\ConfigStruct\exceptions\MissingFieldsException;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Config;
use ReflectionClass;
use ReflectionProperty;
use function dirname;
use function file_exists;
use function is_object;
use function mkdir;

class ConfigStruct
{

    /**
     * You shouldn't create an instance of this class, just run the APIs statically.
     * Look at README.md for usage.
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
     * @throws MissingFieldsException Consider handling this exception and log it onto console.
     */
    public static function parse(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : bool
    {
        if (!file_exists($file)) {
            return false;
        }
        self::parseArray(
            $struct,
            (new Config($file, $type))->getAll(),
            $file
        );
        return true;
    }

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. After the file has been parsed, its content will be copied to this instance of on its structure. Then, if there is a missing field, but its property has an {@link AutoInitializeChildStruct} attribute, that property will be initiated with its child struct recursively.
     * @param array<bool|int|float|string, bool|int|float|string|array> $array Config content in the form of nested scalar keys-values array.
     * @param string|null $file A path that may be included in exceptions when given.
     * @return void
     * @throws MissingFieldsException Consider handling this exception and log it onto console.
     */
    public static function parseArray(
        object  $struct,
        array   $array,
        ?string $file = null
    ) : void
    {
        $reflect = new ReflectionClass($struct);
        foreach (
            $reflect->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            KeyName::getFromProperty($name, $property);
            $value = $array[$name] ?? null;
            if (isset($value)) {
                $struct->$name = $value;
            } elseif (isset($property->getAttributes(
                    Required::class
                )[0])) {
                $missing[] = $property;
            } elseif (AutoInitializeChildStruct::initializeProperty(
                $value,
                $property
            )) {
                $struct->$name = $value;
                continue;
            }

            $group = $property->getAttributes(Group::class)[0] ?? null;
            if (isset($group)) {
                $class = $group->getArguments()[0];
                $child = new $class;
                self::parseArray($child, $value);
            }
        }
        if (isset($missing)) {
            throw new MissingFieldsException($missing, $file);
        }
    }

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. Values of its initiated properties, or uninitiated struct class properties with an {@link AutoInitializeChildStruct} attribute will be encoded recursively in the given type (language) and file path.
     * @param string $file Absolute or related path of the file that the encoded content will be saved to.
     * @param int $type The type (language) of the file (see the constants in {@link Config} class).
     * @return void False = invalid path (file or folder doesn't exists).
     * @throws StructureException When the structure of the given config struct class cannot be recognized (mostly because there is a union-typed property with the {@link AutoInitializeChildStruct} attribute, but the struct class is not specified in the attribute).
     */
    public static function emit(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : void
    {
        @mkdir(dirname($file));
        $config = new Config($file, $type);
        foreach (self::emitArray($struct) as $k => $v) {
            $config->setNested($k, $v);
        }
    }

    /**
     * Look at README.md for examples.
     *
     * @param object $struct An instance of your config struct class. Values of its initiated properties, or uninitiated struct class properties with an {@link AutoInitializeChildStruct} attribute will be encoded recursively in the given type (language) in the form of nested scalar keys-values array and be returned.
     * @return array|null Return a nested scalar keys-values array which holds the encoded content.
     * @throws StructureException When the structure of the given config struct class cannot be recognized (mostly because there is a union-typed property with the {@link AutoInitializeChildStruct} attribute, but the struct class is not specified in the attribute).
     */
    public static function emitArray(object $struct) : ?array
    { // TODO: Not nullable
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            if (!$property->isInitialized()) {
                if (!AutoInitializeChildStruct::initializeProperty($value, $property)) {
                    $class = $struct::class;
                    throw new StructureException("Cannot identify which class to use in $class->{$property->getName()}, please specify the appropriate class in the attribute");
                }
            } else {
                $value = $property->getValue($struct);
            }

            if (is_object($value)) {
                $value = self::emitArray($struct);
            }

            $array[KeyName::getFromProperty($name, $property)] = $value;
        }
        return $array ?? [];
    }

}