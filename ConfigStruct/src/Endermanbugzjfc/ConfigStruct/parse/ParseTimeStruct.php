<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionClass;
use ReflectionProperty;

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

    /**
     * @return ParseTimeProperty[]
     */
    public function scanProperties() : array
    {
        foreach ($this->getReflection()->getProperties(
            ReflectionProperty::IS_PUBLIC
        ) as $property) {
            $return[] = ParseTimeProperty::fromReflection(
                $this,
                $property
            );
        }
        return $return ?? [];
    }

    public function parseValue(
        array $value
    ) : array
    {

    }

    public function copyToBoundOrNewObject(
        array $childValue
    ) : object
    {

    }

}

}