<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use ArrayAccess;
use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use Iterator;
use pocketmine\utils\Utils;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use function array_unique;
use function is_a;

class Analyser
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
     * @param bool $initializeStruct True = initialize the struct during analyse.
     * @return bool True = This struct has default value and was initialized.
     * @throws ReflectionException When {@link Utils::getNiceClassName()} failed.
     * @throws StructureException The class has invalid structure, reason is included in the exception.
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
            self::doesGroupPropertyHasInvalidType($struct, $property);
            self::doesKeyNameHaveDuplicatedArguments($struct, $property);
            self::wasKeyNameAlreadyUsed($struct, $property);
        }
        return $init ?? false;
    }

    /**
     * @param object $struct The class to be checked.
     * @param ReflectionProperty $property The property to be checked from the above class.
     * @throws StructureException If the property has the {@link Group} attribute but a conflicted type.
     * @throws ReflectionException When {@link Utils::getNiceClassName()} failed.
     */
    public static function doesGroupPropertyHasInvalidType(
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
                $structClass = Utils::getNiceClassName($struct);
                throw new StructureException(
                    "Attribute $attributeClass cannot be applied on property $structClass->{$property->getName()}"
                );
            }
        }
    }


    /**
     * @param object $struct The class to be checked.
     * @param ReflectionProperty $property The property to be checked from the above class.
     * @return void
     * @throws StructureException If the property has the {@link Group} attribute but a conflicted type.
     * @throws ReflectionException When {@link Utils::getNiceClassName()} failed.
     */
    public static function doesKeyNameHaveDuplicatedArguments(
        object             $struct,
        ReflectionProperty $property
    ) : void
    {
        $attribute = $property->getAttributes(KeyName::class)[0] ?? null;
        if ($attribute === null) {
            return;
        }

        $names = $attribute->getArguments();
        if ($names !== array_unique($names)) {
            $class = Utils::getNiceClassName($struct);
            throw new StructureException(
                "Property $class->{$property->getName()} has duplicated key names"
            );
        }
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