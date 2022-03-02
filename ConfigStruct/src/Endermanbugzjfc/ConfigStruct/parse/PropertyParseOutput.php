<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

abstract class PropertyParseOutput
{

    abstract public function getValue() : bool|int|float|string|array;

    public function __construct(
        protected ReflectionProperty $reflection,
        protected string             $keyName,
    )
    {
    }

    /**
     * @return ReflectionProperty
     */
    final public function getReflection() : ReflectionProperty
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

}