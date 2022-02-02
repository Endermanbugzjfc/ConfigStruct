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

    public static function emitStruct(
        object $object
    ) : StructEmitOutput
    {
        $reflection = new ReflectionClass($object);

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
        return $property->isInitialized($object);
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
        mixed              $value
    ) : PropertyEmitOutput
    {
    }

}