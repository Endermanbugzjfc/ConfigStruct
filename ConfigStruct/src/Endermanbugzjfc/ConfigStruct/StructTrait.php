<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use ReflectionClass;
use ReflectionProperty;
use TypeError;
use function array_key_exists;
use function array_unshift;

trait StructTrait
{
    /**
     * @return StructParseContext<self>
     */
    public static function parse(array $data) : StructParseContext
    {
        $self = new self();
        $class = new ReflectionClass($self);
        $context = new StructParseContext($class, $self);
        $isPublic = ReflectionProperty::IS_PUBLIC;
        $properties = $class->getProperties($isPublic);

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $rename = Rename::get($property);
            $names = $rename->moreNames;
            array_unshift($names, $rename->name);
            $exists = false;
            foreach ($names as $name) {
                if (array_key_exists($name, $data)) {
                    $typeError = null;
                    try {
                        $self->$propertyName = $data[$name];
                    } catch (TypeError $typeError) {
                    }
                    $exists = true;
                }
            }

            $context->properties[$propertyName] = $property;
            $context->presences[$propertyName] = $exists;
            $context->typeErrors[$propertyName] = $typeError;
        }

        $context->finalize();
        return $context;
    }
}