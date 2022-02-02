<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use ReflectionClass;
use ReflectionProperty;
use function is_callable;

final class Emit
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    public function emitStruct(
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
    }

    public static function emitProperty(
        ReflectionProperty $property,
        mixed              $value
    ) : PropertyEmitOutput
    {
    }

}