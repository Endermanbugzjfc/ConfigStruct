<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\AutoInitializeChildStruct;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Config;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use function class_exists;

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

            self::getKeyName($name, $property);
            $value = $array[$name] ?? null;
            if (isset($value)) {
                $struct->$name = $value;
                continue;
            }

            if (self::initializeChildStruct($value, $property)) {
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
        foreach (
            (new ReflectionClass($struct))->getProperties()
            as $property
        ) {
            if (!$property->isPublic()) {
                continue;
            }
            if (!$property->isInitialized()) {
                self::initializeChildStruct($value, $property);
                if (!isset($value)) {
                    $class = $struct::class;
                    throw new StructureException("Cannot identify which class to use in $class->{$property->getName()}, please specify the appropriate class in the attribute");
                }
            } else {
                $value = $property->getValue($struct);
            }
            $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
            if (isset($attribute)) {
                $name = $attribute->getArguments()[0];
            } else {
                $name = $property->getName();
            }
            $config->setNested($name, $value);
        }
    }

    public static function getKeyName(
        &$value,
        ReflectionProperty $property
    ) : bool
    {
        $value = $property->getName();

        $keyName = $property->getAttributes(KeyName::class)[0] ?? null;
        if (!isset($keyName)) {
            return false;
        }
        $value = $keyName->getArguments()[0];
        return true;
    }

    protected static function initializeChildStruct(&$value, ReflectionProperty $property) : bool
    {
        $default = $property->getAttributes(AutoInitializeChildStruct::class)[0] ?? null;
        if (!isset($default)) {
            return false;
        }
        if (!isset($default->getArguments()[0])) {
            if ($property->getType() instanceof ReflectionNamedType) {
                $class = $property->getName();
            }
        } else {
            $class = $default->getArguments()[0];
        }
        if (isset($class) and class_exists($class)) {
            $value = new $class;
            return true;
        }
        return false;
    }

}