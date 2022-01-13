<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Utils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_search;
use function array_unique;

class Analyse
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    public static function initializeStruct(
        object $struct,
        array  $nodeTrace,
    ) : bool
    {
        $nodeTrace = self::recursion($struct, $nodeTrace);

        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            self::groupPropertyType($struct, $property);
            self::doesKeyNameHaveDuplicatedArgument($struct, $property);
            self::wasKeyNameAlreadyUsed($struct, $property);
        }
        return $init ?? false;
    }

    /**
     * @param ReflectionProperty $property The property to be checked.
     * @return void
     * @throws StructureException The property has invalid structure.
     */
    public static function property(
        ReflectionProperty $property,
    ) : void
    {
        $blameProperty = "Property {$property->getDeclaringClass()->getName()}->{$property->getName()}";

        $keyName = $property->getAttributes(KeyName::class)[0] ?? null;
        if (
            $keyName !== null
            and
            self::doesKeyNameHaveDuplicatedArgument($keyName)
        ) {
            throw new StructureException(
                "$blameProperty used two key names which is exactly the same"
            );
        }


        $group = $property->getAttributes(Group::class)[0] ?? null;
        if (
            $group !== null
            and
            self::doesGroupPropertyHaveInvalidType($property)
        ) {
            throw new StructureException(
                "$blameProperty is a group but doesn't use the \"array\" type or include it in union-types"
            );
        }
    }

    /**
     * @param ReflectionProperty $property The property to be checked.
     * @return bool The property's type is compatible with a {@link Group} attribute.
     */
    public static function doesGroupPropertyHaveInvalidType(
        ReflectionProperty $property
    ) : bool
    {
        $types = $property->getType();
        if ($types instanceof ReflectionNamedType) {
            if ($types->getName() === "array") {
                return true;
            }
        } elseif (!$types === null) {
            foreach ($types->getTypes() as $type) {
                if ($type->getName() === "array") {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @param ReflectionAttribute $attribute The attribute to be checked.
     * @return bool True = two or more arguments in the attribute have the same value and type.
     */
    public static function doesKeyNameHaveDuplicatedArgument(
        ReflectionAttribute $attribute
    ) : bool
    {
        $names = $attribute->getArguments();
        return $names !== array_unique($names);
    }

    /**
     * @param object $struct The child struct to be checked.
     * @param string[] $nodeTrace Variable reference to a stacktrace. Class name of the child struct will be appended to this.
     * @return bool True = THe child struct is recursive but doesn't have the {@link Recursive} attribute.
     * @throws ReflectionException
     */
    public static function recursion(
        object $struct,
        array  &$nodeTrace
    ) : bool
    {
        $class = Utils::getNiceClassName($struct);
        $nodeTrace[] = $class;

        $r = array_search($class, $nodeTrace, true);
        if ($r !== false) {
            return empty(
            (new ReflectionClass(
                $nodeTrace[$r]
            ))->getAttributes(Recursive::class));
        }

        return false;
    }

}