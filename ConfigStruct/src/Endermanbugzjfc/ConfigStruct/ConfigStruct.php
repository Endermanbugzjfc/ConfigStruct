<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\AutoInitializeChildStruct;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Config;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;
use function class_exists;

class ConfigStruct
{

    private function __construct()
    {
    }

    public static function parse(
        string $file,
        object $struct,
        int    $type = Config::DETECT
    ) : ?Throwable
    {
        $reflect = new ReflectionClass($struct);
        $names = [];
        foreach ($reflect->getProperties() as $property) {
            if (!$property->isPublic()) {
                continue;
            }

            $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
            $name = $property->getName();
            if (!isset($attribute)) {
                $names[$name] = $name;
                continue;
            }
            $names[$name] = $attribute->getArguments()[0];

            $value = self::initializeChildStruct($property);
            if (isset($value)) {
                $struct->$name = $value;
            }
        }
        foreach (
            (new Config($file, $type))->getAll()
            as $k => $v
        ) {
            $name = $names[$k];
            $struct->$name = $v;
        }
        return null;
    }

    /**
     * @throws StructureException
     */
    public static function emit(
        string $file,
        object $struct,
        int    $type = Config::DETECT
    ) : ?Throwable
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
                $value = self::initializeChildStruct($property);
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
            $config->setNested($name, $property->getValue());
        }
        return null;
    }

    protected static function initializeChildStruct(ReflectionProperty $property) : ?object
    {
        $default = $property->getAttributes(AutoInitializeChildStruct::class)[0] ?? null;
        if (!isset($default)) {
            return null;
        }
        if (!isset($default->getArguments()[0])) {
            if ($property->getType() instanceof ReflectionNamedType) {
                $class = $property->getName();
            }
        } else {
            $class = $default->getArguments()[0];
        }
        return (isset($class) and class_exists($class)) ? new $class : null;
    }

}