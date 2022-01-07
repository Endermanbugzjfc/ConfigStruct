<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use pocketmine\utils\Config;
use ReflectionClass;
use Throwable;

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
            $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
            $name = $property->getName();
            if (!isset($attribute)) {
                $names[$name] = $name;
                continue;
            }
            $names[$name] = $attribute->getArguments()[0];
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

}