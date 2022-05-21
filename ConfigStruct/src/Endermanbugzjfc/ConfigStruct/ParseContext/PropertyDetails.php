<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use ReflectionProperty;

/**
 * A wrapper class to be passed between parser functions. Without the need to make breaking changes when more property details have been introduced.
 */
final class PropertyDetails
{
    public function __construct(
        protected string $keyName, // TODO: Support int key.
        protected ReflectionProperty $reflection
    ) {
    }


    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }


    public function getKeyName() : string
    {
        return $this->keyName;
    }
}