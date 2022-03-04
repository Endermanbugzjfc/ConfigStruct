<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use ReflectionProperty;
use RuntimeException;
use Throwable;

class BasePropertyContext
{
    /**
     * This method should never be called unless from a "non-abstract" property parse context.
     * @return mixed
     */
    public function getValue() : mixed
    {
        throw new RuntimeException(
            "Trying to get value from an abstract property parse context"
        );
    }

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     */
    public function __construct(
        protected string             $keyName,
        protected ReflectionProperty $reflection
    )
    {
    }

    final protected function substitute(
        self $defaultContext
    ) : void
    {
        $this->keyName = $defaultContext->getKeyName();
        $this->reflection = $defaultContext->getReflection();
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
        return [];
    }

}