<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Closure;
use ReflectionClass;
use ReflectionProperty;
use function is_callable;
use function is_object;

final class Emit
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    /**
     * @param object $object The object to be emitted. Basically flatten to array base on its structure (property types, attributes, etc...).
     * @return StructEmitOutput
     */
    public static function emitStruct(
        object $object
    ) : StructEmitOutput
    {
        $reflection = new ReflectionClass($object);
        self::emitStructReflection(
            $reflection,
            $object
        );
    }

    /**
     * @see Emit::emitStruct()
     * @param ReflectionClass $reflection Reflection is required to identify the object's structure.
     * @param object $object
     * @return StructEmitOutput
     */
    public static function emitStructReflection(
        ReflectionClass $reflection,
        object          $object
    ) : StructEmitOutput
    {
        foreach ($reflection->getProperties(
            ReflectionProperty::IS_PUBLIC
        ) as $property) {
            if (self::shouldSkipProperty(
                $property,
                $object
            )) {
                $skipped[$property->getName()] = $property;
                continue;
            }
            $output = self::emitProperty(
                $property,
                $property->getValue($object)
            );
            $outputs[$property->getName()] = is_callable($output)
                ? $output()
                : $output;
        }

        return StructEmitOutput::create(
            $reflection,
            $outputs ?? [],
            $skipped ?? []
        );
    }

    protected static function shouldSkipProperty(
        ReflectionProperty $property,
        object             $object
    ) : bool
    {
        return !$property->isInitialized($object);
    }

    public static function emitProperty(
        ReflectionProperty $property,
        mixed              $value
    ) : PropertyEmitOutput|Closure
    {
        if (is_object($value)) {
            return fn() => self::emitChildStruct(
                $property,
                $value
            );
        }

        // TODO: array

        return RawEmitOutput::create(
            $property,
            $value
        );
    }

    public static function emitChildStruct(
        ReflectionProperty $property,
        object             $value
    ) : PropertyEmitOutput
    {
        return ChildStructEmitOutput::create(
            $property,
            self::emitStruct(
                $value
            )
        );
    }

}