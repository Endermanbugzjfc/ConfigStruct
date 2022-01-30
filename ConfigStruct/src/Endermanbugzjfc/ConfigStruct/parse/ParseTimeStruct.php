<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionClass;

final class ParseTimeStruct
{

    private function __construct(
        protected ReflectionClass $reflection
    )
    {

    }

    public static function fromReflection(
        ReflectionClass $reflection
    ) : self
    {
        return new self($reflection);
    }

    /**
     * @returnReflectionClass
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

}