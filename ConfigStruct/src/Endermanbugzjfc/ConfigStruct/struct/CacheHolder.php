<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function is_object;

class CacheHolder
{

    /**
     * @var array<string, object>
     */
    public array $structsCache = [];

    /**
     * @throws ReflectionException
     */
    public function copyToStruct(object $struct) : bool
    {
        $class = Utils::getNiceClassName($struct);
        $cache = $this->structsCache[$class] ?? null;
        if ($cache === null) {
            return false;
        }

        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            $name = $property->getName();
            $value = $cache->$name;
            if (is_object($value)) {
                $value = clone $value;
            }
            $struct->$name = $value;
        }
        return true;
    }

}