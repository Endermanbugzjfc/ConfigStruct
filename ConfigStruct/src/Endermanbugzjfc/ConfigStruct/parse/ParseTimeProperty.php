<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ParseTimeProperty
{

    private function __construct(
        protected ParseTimeStruct    $owner,
        protected ReflectionProperty $reflection
    )
    {

    }

    public static function fromReflection(
        ParseTimeStruct    $owner,
        ReflectionProperty $reflection
    ) : self
    {
        return new self($owner, $reflection);
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return ParseTimeStruct
     */
    public function getOwner() : ParseTimeStruct
    {
        return $this->owner;
    }

}