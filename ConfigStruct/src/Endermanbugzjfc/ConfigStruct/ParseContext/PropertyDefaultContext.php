<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use ReflectionProperty;
use Throwable;

class PropertyDefaultContext
{
    /**
     * @return mixed
     */
    public function getValue() : mixed
    {
        return $this->value;
    }

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     * @param Throwable[] $errors Parse time errors.
     * @param mixed $value
     */
    public function __construct(
        protected string             $keyName,
        protected ReflectionProperty $reflection,
        protected array $errors,
        protected mixed $value
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
     * @param string $keyName
     */
    public function setKeyName(string $keyName) : void
    {
        $this->keyName = $keyName;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

}