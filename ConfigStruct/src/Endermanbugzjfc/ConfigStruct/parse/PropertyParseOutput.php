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

    /**
     * @param ReflectionProperty $reflection Reflection of the property.
     * @param string $keyName The key name will be inconsistent for the same property if it has more than one custom key names.
     * @param mixed $output Parse output which haven't been flattened.
     */
    protected function __construct(
        protected ReflectionProperty $reflection,
        protected string             $keyName,
        protected mixed              $output
    )
    {
    }

    /**
     * @param ReflectionProperty $reflection Reflection of the property.
     * @param string $keyName The key name will be inconsistent for the same property if it has more than one custom key names.
     * @param mixed $output Parse output which haven't been flattened.
     */
    abstract public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed $output
    ) : self;

    /**
     * @return ReflectionProperty Reflection of the property.
     */
    final public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return string The key name will be inconsistent for the same property if it has more than one custom key names.
     */
    public function getKeyName() : string
    {
        return $this->keyName;
    }

}