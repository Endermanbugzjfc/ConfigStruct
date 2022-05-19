<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use ReflectionClass;
use ReflectionProperty;
use function array_key_exists;
use function array_unshift;

trait StructTrait
{
    public static function parse(array $data) : self
    {
        $self = new self();
        $class = new ReflectionClass(self::class);
        $isPublic = ReflectionProperty::IS_PUBLIC;
        $properties = $class->getProperties($isPublic);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $rename = Rename::get($property);
            $names = $rename->moreNames;
            array_unshift($names, $rename->name);
            $ok = false;
            foreach ($names as $name) {
                if (array_key_exists($name, $data)) {
                    $self->$propertyName = $data[$name];
                    $ok = true;
                }
            }
        }

        return $self;
    }
}