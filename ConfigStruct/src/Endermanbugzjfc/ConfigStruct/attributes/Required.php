<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\utils\AttributeUtils;
use ReflectionAttribute;
use ReflectionProperty;
use function assert;

#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public function __construct(callable $onMissing)
    {
    }

    public static function isFieldMissing(
        object             $struct,
        ReflectionProperty $property
    ) : bool
    {
        if (AttributeUtils::trueIfNoInProperty(
            $property,
            self::class,
            $attribute
        )) {
            return false;
        }
        assert($attribute instanceof ReflectionAttribute);

        $n = $property->getValue();
        if (isset($struct->$n)) {
            return false;
        }
        return true;
    }

}