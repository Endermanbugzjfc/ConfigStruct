<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Endermanbugzjfc\ConfigStruct\KeyName;
use ReflectionProperty;

abstract class PropertyEmitOutput
{

    abstract public function getFlattenedValues() : mixed;

    protected function __construct(
        protected ReflectionProperty $reflection,
        protected mixed              $output
    )
    {
    }

    abstract public static function create(
        ReflectionProperty $reflection,
        mixed              $output
    ) : self;

    /**
     * @return ReflectionProperty
     */
    final public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    public function getKeyName() : string
    {
        $keyName = $this->getReflection()->getAttributes(
                KeyName::class
            )[0] ?? null;
        if ($keyName !== null) {
            return $keyName;
        }
        return $this->getReflection()->getName();
    }

}