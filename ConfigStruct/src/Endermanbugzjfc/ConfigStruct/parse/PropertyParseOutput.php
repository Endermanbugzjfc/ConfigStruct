<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

abstract class PropertyParseOutput
{

    abstract protected function getFlattenedValue() : mixed;

    public function copyToObject(
        object $object,
    ) : object
    {
        $this->getReflection()->setValue($object, $this->getFlattenedValue());
        return $object;
    }

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     */
    private function __construct(
        protected ReflectionProperty $reflection,
        protected string             $keyName,
        protected mixed              $output
    )
    {
    }

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     */
    abstract public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output
    ) : self;

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

}