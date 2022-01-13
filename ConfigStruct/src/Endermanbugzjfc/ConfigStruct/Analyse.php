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

    /**
     * @throws ReflectionException
     * @throws StructureException
     */
    public static function struct(
        object $struct,
        array  $nodeTrace,
    ) : void
    {
        $class = Utils::getNiceClassName($struct);
        $sClass = self::recursion($struct, $nodeTrace);
        if ($sClass !== null) {
            throw new StructureException(
                "Recursion found in struct class $sClass => ... => $class => loop"
            );
        }

        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            self::property($property);
        }
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
                "$blameProperty is a group but its type is not compatible"
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
        if ($types === null) {
            return true;
        } elseif ($types instanceof ReflectionNamedType) {
            if ($types->getName() === "array") {
                return true;
            }
        } else {
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
     * @return string|null Class name of the recursive struct. Null = no recursion.
     * @phpstan-return class-string
     * @throws ReflectionException
     */
    public static function recursion(
        object $struct,
        array  &$nodeTrace
    ) : ?string
    {
        $class = Utils::getNiceClassName($struct);
        $nodeTrace[] = $class;

        $r = array_search($class, $nodeTrace, true);
        if ($r !== false) {
            return empty(
            (new ReflectionClass(
                $sClass = $nodeTrace[$r]
            ))->getAttributes(Recursive::class))
                ? $sClass : null;
        }

        return null;
    }

}