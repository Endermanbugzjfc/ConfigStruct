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
use function file_exists;
use function is_object;

class ConfigStruct
{

    private function __construct()
    {
    }

    /**
     * @throws MissingFieldsException
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
     * @throws MissingFieldsException
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