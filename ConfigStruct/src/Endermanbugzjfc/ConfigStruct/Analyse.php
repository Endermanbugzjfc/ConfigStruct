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
use function class_exists;
use function count;

class Analyse
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    /**
     * @param ReflectionClass $class The struct to be checked.
     * @param ReflectionClass[] $nodeTrace A stacktrace which contains {@link ReflectionClass} instances of child structs.
     * @return void
     * @throws ReflectionException
     * @throws StructureException When {@link Analyse::recursion()} or {@link Analyse::property()} fails.
     */
    public static function struct(
        ReflectionClass $class,
        array           $nodeTrace
    ) : void
    {
        $end = self::recursion($class, $nodeTrace);
        if ($end !== null) {
            $niceClass = Utils::getNiceClassName(
                $class->newInstanceWithoutConstructor()
            );
            $niceEnd = Utils::getNiceClassName(
                $end->newInstanceWithoutConstructor()
            );
            throw new StructureException(
                "Recursion found in struct class $niceClass => ... => $niceEnd => loop"
            );
        }

        foreach (
            $class->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            self::property($property);

            $types = $property->getType() ?? [];
            if ($types !== null) {
                $types = $types instanceof ReflectionNamedType
                    ? [$types]
                    : $types->getTypes();
            }
            foreach ($types as $type) {
                if (class_exists($type->getName())) {
                    self::struct(new ReflectionClass(
                        $type->getName()
                    ), $nodeTrace);
                }
            }
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
     * @param ReflectionClass $class The child struct to be checked.
     * @param ReflectionClass[] $nodeTrace Variable reference to a stacktrace. A {@link ReflectionClass} instance of the child struct will be appended to this.
     * @return ReflectionClass|null The ending-struct of this recursion. Null = no recursion.
     * @throws ReflectionException
     */
    public static function recursion(
        ReflectionClass $class,
        array           &$nodeTrace
    ) : ?ReflectionClass
    {
        foreach ($nodeTrace as $sClass) {
            if ($sClass->getName() === $class->getName()) {
                $recursion = true;
            }
        }

        $nodeTrace[] = $class;
        if (!isset($recursion) or !empty(
            $class->getAttributes(Recursive::class)
            )) {
            return null;
        }

        return $nodeTrace[count($nodeTrace) - 2];
    }

}