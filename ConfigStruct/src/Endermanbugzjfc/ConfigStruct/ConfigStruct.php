<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\AutoInitializeChildStruct;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Config;
use ReflectionClass;

class ConfigStruct
{

    private function __construct()
    {
    }

    public static function parse(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : void
    {
        self::parseArray($struct, (new Config($file, $type))->getAll());
    }

    public static function parseArray(object $struct, array $array) : void
    {
        $reflect = new ReflectionClass($struct);
        foreach ($reflect->getProperties() as $property) {
            if (!$property->isPublic()) {
                continue;
            }

            KeyName::getFromProperty($name, $property);
            $value = $array[$name] ?? null;
            if (isset($value)) {
                $struct->$name = $value;
                continue;
            }

            if (AutoInitializeChildStruct::initializeProperty($value, $property)) {
                $struct->$name = $value;
            }
        }
    }

    /**
     * @throws StructureException
     */
    public static function emit(
        object $struct,
        string $file,
        int    $type = Config::DETECT
    ) : void
    {
        $config = new Config($file, $type);
        foreach (self::emitArray($struct) as $k => $v) {
            $config->setNested($k, $v);
        }
    }

    /**
     * @throws StructureException
     */
    public static function emitArray(object $struct) : ?array
    {
        foreach (
            (new ReflectionClass($struct))->getProperties()
            as $property
        ) {
            if (!$property->isPublic()) {
                continue;
            }

            if (!$property->isInitialized()) {
                if (!AutoInitializeChildStruct::initializeProperty($value, $property)) {
                    $class = $struct::class;
                    throw new StructureException("Cannot identify which class to use in $class->{$property->getName()}, please specify the appropriate class in the attribute");
                }
            } else {
                $value = $property->getValue($struct);
            }
            $array[KeyName::getFromProperty($name, $property)] = $value;
        }
        return $array ?? [];
    }

}