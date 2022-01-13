<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
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
            self::groupPropertyType($struct, $property);
            self::doesKeyNameHaveDuplicatedArgument($struct, $property);
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

}