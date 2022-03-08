<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

trait StructureErrorThrowerTrait
{

    private static function invalidStructure(
        Throwable                          $previous,
        ReflectionClass|ReflectionProperty $classOrProperty
    ) : void
    {
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