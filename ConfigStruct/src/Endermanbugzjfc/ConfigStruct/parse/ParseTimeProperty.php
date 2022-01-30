<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ParseTimeProperty
{

    private function __construct(
        protected ReflectionProperty $reflection
    )
    {

    }

    public static function fromReflection(
        ReflectionProperty $reflection
    ) : self
    {
        return new self($reflection);
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

}