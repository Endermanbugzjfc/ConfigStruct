<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionClass;
use ReflectionProperty;

final class ParseStruct
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

    /**
     * @return ParseProperty[]
     */
    public function scanProperties() : array
    {
        foreach ($this->getReflection()->getProperties(
            ReflectionProperty::IS_PUBLIC
        ) as $property) {
            $return[] = ParseProperty::fromReflection(
                $this,
                $property
            );
        }
        return $return ?? [];
    }

}