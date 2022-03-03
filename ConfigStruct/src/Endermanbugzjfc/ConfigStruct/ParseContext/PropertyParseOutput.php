<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use ReflectionProperty;
use Throwable;

abstract class PropertyParseOutput
{

    abstract public function getValue() : mixed;

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     * @param Throwable[] $errors Parse time errors.
     */
    public function __construct(
        protected string             $keyName,
        protected ReflectionProperty $reflection,
        protected array $errors
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

    /**
     * @return Throwable[]
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

}