<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionClass;
use ReflectionProperty;

final class ParseTimeStruct
{

    protected object $boundObject;

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

    public static function fromObject(
        object $object
    ) : self
    {
        $self = self::fromReflection(new ReflectionClass($object));
        $self->boundObject = $object;
        return $self;
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
    ) : ParseResultForStruct
    {
        foreach ($this->scanProperties() as $property) {
            $property->bindKeyNameIgnoreExistenceInData($value);
            $parsed[$property->getReflection()->getName()] = $property
                ->parseValue($value);
        }

        return ParseResultForStruct::create(
            $parsed ?? [],
            [], // TODO
            []
        );
    }

    public function copyToBoundOrNewObject(
        array $childValue
    ) : object
    {

    }

}

}