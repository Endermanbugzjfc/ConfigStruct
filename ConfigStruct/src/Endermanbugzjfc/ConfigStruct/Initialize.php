<?php

namespace Endermanbugzjfc\ConfigStruct;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use function class_exists;

final class Initialize
{
    /**
     * @param object $struct The struct to be initialized, it should be already analysed using {@link Analyse::struct()}.
     * @return void
     */
    public function struct(object $struct) : void
    {
        foreach (
            (new ReflectionClass($struct))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            $type = $property->getType();
            if (
                (!$type instanceof ReflectionNamedType)
                or
                !class_exists($sClass = $type->getName())
            ) {
                continue;
            }

            $property->setValue($struct, new $sClass);
        }
    }

}