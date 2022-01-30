<?php

namespace Endermanbugzjfc\ConfigStruct;

use DaveRandom\CallbackValidator\CallbackType;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\attributes\TypedArray;
use Error;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use function array_unique;
use function class_exists;
use function count;

/**
 * Struct analysing is not forced to be used.
 * You may want to disable it on production code for performance purpose.
 */
final class Analyse
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
     */
    public static function struct(
        ReflectionClass $class,
        array           $nodeTrace
    ) : void
    {
        $constructor = $class->getConstructor();
        if (
            $constructor !== null
            and
            !self::doesStructHaveValidConstructor($constructor)
        ) {
            throw new StructureException(
                "Constructor of struct class {$class->getName()} should be public and have 0 arguments"
            );
        }

        $end = self::recursion($class, $nodeTrace);
        if ($end !== null) {
            throw new StructureException(
                "Recursion found in struct class {$class->getName()} => ... => {$end->getName()} => loop"
            );
        }

        foreach (
            $class->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            self::property($property);

            $type = $property->getType();
            if (
                ($type instanceof ReflectionNamedType)
                and
                class_exists($type->getName())
            ) {
                try {
                    self::struct(new ReflectionClass(
                        $type->getName()
                    ), $nodeTrace);
                } catch (ReflectionException $err) {
                    throw new Error($err);
                }
            }

            $group = $property->getAttributes(TypedArray::class)[0] ?? null;
            if ($group !== null) {
                try {
                    self::struct(new ReflectionClass(
                        $group->getArguments()[0]
                    ), $nodeTrace);
                } catch (ReflectionException $err1) {
                    throw new Error($err1);
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
        $blameProperty = "Property {$property->getDeclaringClass()}->{$property->getName()}";

        if (
            self::doesKeyNameHaveDuplicatedArgument(
                ...$property
                ->getAttributes(KeyName::class)
            )
        ) {
            throw new StructureException(
                "$blameProperty used two key names which is exactly the same"
            );
        }


        $group = $property->getAttributes(TypedArray::class)[0] ?? null;
        if ($group !== null) {
            if (self::doesGroupPropertyHaveInvalidType($property)) {
                throw new StructureException(
                    "$blameProperty is a group but its type is not compatible"
                );
            }
            if (self::doesGroupAttributeHaveInvalidClassArgument($group)) {
                throw new StructureException(
                    "$blameProperty has a group attribute with invalid class string"
                );
            }
        }

        if (self::doesPropertyHaveUnionTypesChildStruct($property)) {
            throw new StructureException(
                "$blameProperty used union-types child struct which is not supported in this ConfigStruct version"
            );
        }
    }

    /**
     * @param ReflectionProperty $property The property to be checked.
     * @return bool The property's type is compatible with a {@link TypedArray} attribute.
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
     * @param ReflectionAttribute ...$attributes The attribute to be checked.
     * @return bool True = two or more arguments in the attribute have the same value and type.
     */
    public static function doesKeyNameHaveDuplicatedArgument(
        ReflectionAttribute ...$attributes
    ) : bool
    {
        foreach ($attributes as $attribute) {
            $names[] = $attribute->getArguments()[0];
        }
        if (!isset($names)) {
            return false;
        }

        return $names !== array_unique($names);
    }

    /**
     * @param ReflectionClass $class The child struct to be checked.
     * @param ReflectionClass[] $nodeTrace Variable reference to a stacktrace. A {@link ReflectionClass} instance of the child struct will be appended to this.
     * @return ReflectionClass|null The ending-struct of this recursion. Null = no recursion.
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

    public static function doesStructHaveValidConstructor(
        ReflectionMethod $constructor
    ) : bool
    {
        if (!$constructor->isPublic()) {
            return false;
        }
        return CallbackType::createFromCallable(function () {
        })->isSatisfiedBy([
            $constructor->getDeclaringClass()->getName(),
            $constructor->getName()
        ]);
    }

    public static function doesPropertyHaveUnionTypesChildStruct(
        ReflectionProperty $property
    ) : bool
    {
        $types = $property->getType();
        if ($types === null or ($types instanceof ReflectionNamedType)) {
            return false;
        }
        foreach ($types->getTypes() as $type) {
            if (class_exists($type->getName())) {
                return true;
            }
        }
        return false;
    }

    public static function doesGroupAttributeHaveInvalidClassArgument(
        ReflectionAttribute $group
    ) : bool
    {
        return class_exists($group->getArguments()[0]);
    }

}