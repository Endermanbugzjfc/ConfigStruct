<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

abstract class PropertyParseOutput
{

    abstract protected function getFlattenedValue() : mixed;

    /**
     * @param object $object Overwrites one specified property in this object.
     * @return object === $object.
     */
    public function copyToObject(
        object $object,
    ) : object
    {
        $this->getReflection()->setValue($object, $this->getFlattenedValue());
        return $object;
    }

    public function __construct(
        protected ReflectionProperty $reflection,
        protected string             $keyName,
        mixed                        $output
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