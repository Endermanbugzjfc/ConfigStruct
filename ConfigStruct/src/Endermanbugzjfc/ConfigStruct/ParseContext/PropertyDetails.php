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
        protected string $keyName,
        protected ReflectionProperty $reflection
    )
    {
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return string
     */
    public function getKeyName() : string
    {
        return $this->keyName;
    }

    /**
     * @param string $keyName
     */
    public function setKeyName(string $keyName) : void
    {
        $this->keyName = $keyName;
    }

}