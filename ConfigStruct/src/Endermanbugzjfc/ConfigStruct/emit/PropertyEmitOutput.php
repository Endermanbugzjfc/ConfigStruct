<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\parse\PropertyParseOutput;
use ReflectionProperty;

abstract class PropertyEmitOutput
{

    abstract public function getFlattenedValue() : mixed;

    /**
     * @param ReflectionProperty $reflection Reflection of the property.
     * @param mixed $Parse Emit output which haven't been flattened.
     */
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
     * @return ReflectionProperty Reflection of the property.
     */
    final public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return string The property name or the first custom key name candidate if there is one. Unlike {@link PropertyParseOutput::getKeyName()}, the return value of this function should always be consistent for the same property.
     */
    public function getKeyName() : string
    {
        $keyName = $this->getReflection()->getAttributes(
                KeyName::class
            )[0] ?? null;
        if ($keyName !== null) {
            return $keyName->getArguments()[0];
        }
        return $this->getReflection()->getName();
    }

}