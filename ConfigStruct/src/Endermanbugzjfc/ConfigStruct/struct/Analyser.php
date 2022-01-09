<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use ReflectionUnionType;
use function class_exists;
use function count;

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
            throw new StructureException(
                "Recursion found in struct class $nodeTrace[$r] => ... => $class"
            );
        }
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            $type = $property->getType();
            if ($type === null) {
                continue;
            } elseif ($type instanceof ReflectionUnionType) {
                $types = $type->getTypes();
            } else {
                $types = [$type];
            }
            switch (count($types)) {
                case 1:
                    $sStruct = $types[0];
                    break;
                case 2:
                    switch ("array") {
                        case $types[0]:
                            $sStruct = $types[1];
                            break;
                        case $types[1]:
                            $sStruct = $types[0];
                            break;
                    }
                    break;
            }
            if (
                !isset($sStruct)
                or
                !class_exists($class = $sStruct->getName())
            ) {
                continue;
            }
            $init = true;
            $sInstance = new $class;

            $initialized = self::analyseStruct(
                $sInstance,
                $nodeTrace,
                $initializeStruct
            );

            if ($initializeStruct) {
                if (count($types) === 2) {
                    $property->setValue(
                        $struct,
                        $initialized
                            ? [$sInstance]
                            : []
                    );
                } else {
                    $property->setValue($struct, $sInstance);
                }
            }
        }
        return $init ?? false;
    }

}