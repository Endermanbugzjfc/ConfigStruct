<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

trait StructureErrorThrowerTrait
{
    /**
     * @phpstan-ignore-next-line PHPDoc tag @template T for method Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext::invalidStructure() shadows @template T of object for class Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext.
     * @template T of object
     * @param ReflectionClass<T>|ReflectionProperty $classOrProperty
     */
    private static function invalidStructure(
        Throwable                          $previous,
        ReflectionClass|ReflectionProperty $classOrProperty
    ) : void {
        if ($classOrProperty instanceof ReflectionClass) {
            $className = $classOrProperty->getName();
            $propertyName = "";
        } else {
            $className = $classOrProperty->getDeclaringClass()->getName();
            $propertyName = "->" . $classOrProperty->getName();
        }

        throw new StructureError(
            "Invalid structure in " . $className . $propertyName,
            $previous
        );
    }
}