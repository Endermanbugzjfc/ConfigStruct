<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Analyser
{

    private function __construct()
    {
    }

    /**
     * @throws ReflectionException
     * @throws StructureException
     */
    public static function analyseStruct(
        object $struct,
        array  $nodeTrace,
        bool   $initializeStruct = true,
    ) : bool
    {
        $class = Utils::getNiceClassName($struct);
        if (($r = array_search($class, $nodeTrace, true)) !== false) {
            if (!empty((new ReflectionClass(
                $nodeTrace[$r]
            ))->getAttributes(Recursive::class))) {
                return false;
            }
            throw new StructureException(
                "Recursion found in struct class $nodeTrace[$r] => ... => $class"
            );
        }
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
        }
        return $init ?? false;
    }

}