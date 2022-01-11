<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use ArrayAccess;
use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use Iterator;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function is_a;

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
            self::checkGroup($struct, $property);
        }
        return $init ?? false;
    }

    /**
     * @param object $struct
     * @param ReflectionProperty $property
     * @throws StructureException
     */
    public static function checkGroup(
        object             $struct,
        ReflectionProperty $property
    ) : void
    {
        $attribute = $property->getAttributes(Group::class)[0] ?? null;
        if ($attribute === null) {
            return;
        }

        $types = $property->getType();
        if ($types instanceof ReflectionNamedType) {
            $types = [$types];
        } else {
            $types = $types->getTypes();
        }
        foreach ($types as $type) {
            if (
                $type->getName() !== "array"
                and
                !(
                    is_a(
                        $type->getName(),
                        ArrayAccess::class,
                        true
                    )
                    and
                    is_a(
                        $type->getName(),
                        Iterator::class,
                        true
                    )
                )
            ) {
                $attributeClass = Group::class;
                $structClass = $struct::class;
                throw new StructureException(
                    "Attribute $attributeClass cannot be applied on property $structClass->{$property->getName()}"
                );
            }
        }
    }

}