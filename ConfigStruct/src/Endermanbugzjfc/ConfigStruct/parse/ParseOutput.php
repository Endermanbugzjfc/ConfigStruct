<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

abstract class ParseOutput
{

    abstract protected function getFlattenedValue() : mixed;

    public function copyToObject(
        object             $object,
        ReflectionProperty $property
    ) : object
    {
        $property->setValue($object, $this->getFlattenedValue());
        return $object;
    }

}