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
        $sClass = self::recursion($struct::class, $nodeTrace);
        if ($sClass !== null) {
            $niceClass = Utils::getNiceClassName($struct);
            throw new StructureException(
                "Recursion found in struct class $sClass => ... => $niceClass => loop"
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
            return false;
        } elseif ($types instanceof ReflectionNamedType) {
            if (
                $types->getName() === "array"
                or
                $types->getName() === "mixed"
            ) {
                return false;
            }
        } else {
            foreach ($types->getTypes() as $type) {
                if ($type->getName() === "array") {
                    return false;
                }
            }
        }
        return true;
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
     * @param string $class Class name of the child struct to be checked.
     * @param string[] $nodeTrace Variable reference to a stacktrace. Class name of the child struct will be appended to this.
     * @return string|null Class name of the ending-struct in this recursion. Null = no recursion.
     * @throws ReflectionException
     * @phpstan-return class-string
     */
    public static function recursion(
        string $class,
        array  &$nodeTrace
    ) : ?string
    {
        $nodeTrace[] = $class;

        $r = array_search($class, $nodeTrace, true);
        if ($r !== false) {
            return empty(
            (new ReflectionClass(
                $r
            ))->getAttributes(Recursive::class))
                ? $class : null;
        }

        return null;
    }

}