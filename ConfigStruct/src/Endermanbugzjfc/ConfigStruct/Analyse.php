<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_unique;

class Analyse
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    /**
     * @param object $struct The class to be analysed.
     * @param string[] $nodeTrace An array of class string. Class string of $struct on the top, class string of the root struct at the bottom.
     * @phpstan-param class-string[] $nodeTrace
     *
     * @return bool True = This struct has default value and was initialized.
     * @throws ReflectionException When {@link Utils::getNiceClassName()} failed.
     * @throws StructureException The class has invalid structure, reason is included in the exception.
     */
    public static function initializeStruct(
        object $struct,
        array  $nodeTrace,
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
            self::doesGroupPropertyHasInvalidType($struct, $property);
            self::doesKeyNameHaveDuplicatedArguments($struct, $property);
            self::wasKeyNameAlreadyUsed($struct, $property);
        }
        return $init ?? false;
    }

    public static function property(
        string             $class,
        ReflectionProperty $property
    ) : void
    {
        self::doesGroupPropertyHasInvalidType($class, $property);
        self::doesKeyNameHaveDuplicatedArguments($class, $property);
        self::wasKeyNameAlreadyUsed($class, $property);
    }

    /**
     * @param ReflectionProperty $property The property to be checked.
     * @return bool True = the property has a {@link Group} attribute but doesn't use the "array" type or include it in union-types.
     */
    public static function doesGroupPropertyHasInvalidType(
        ReflectionProperty $property
    ) : bool
    {
        $attribute = $property->getAttributes(Group::class)[0] ?? null;
        if ($attribute === null) {
            return false;
        }

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
     * @param ReflectionProperty $property The property to be checked.
     * @return bool True = the property has a {@link KeyName} attribute but two or more of its arguments have the same value and type.
     */
    public static function doesKeyNameHaveDuplicatedArguments(
        ReflectionProperty $property
    ) : bool
    {
        $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
        if ($attribute === null) {
            return false;
        }

        $names = $attribute->getArguments();
        return $names !== array_unique($names);
    }


    /**
     * @param object $struct The class to be checked.
     * @param ReflectionProperty $property The property to be checked from the above class.
     * @return void
     * @throws StructureException If the property has the {@link Group} attribute but a conflicted type.
     * @throws ReflectionException When {@link Utils::getNiceClassName()} failed.
     */
    public static function wasKeyNameAlreadyUsed(
        object             $struct,
        ReflectionProperty $property
    ) : void
    {
        $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
        if ($attribute === null) {
            return;
        }

        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $sProperty
        ) {
            $name = (
                $sProperty->getAttributes(Group::class)[0]
                    ?->getArguments()[0]
                ) ?? $sProperty->getName();
            if ($attribute->getArguments()[0] === $name) {
                $class = Utils::getNiceClassName($struct);
                throw new StructureException(
                    "Key name \"$name\" of property $class->{$property->getName()} was already used by property $class->{$sProperty->getName()}"
                );
            }
        }
    }

}