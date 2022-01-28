<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use ArrayAccess;
use Attribute;
use Endermanbugzjfc\ConfigStruct\Parse;
use Iterator;

#[Attribute(Attribute::TARGET_PROPERTY)] class TypedArray
{

    /**
     * This attribute is an indicator for {@link Parse} to convert array elements to child structs.
     *
     * Property that uses this attribute should has the type array or classes which implement the {@link ArrayAccess} and {@link Iterator} interface.
     *
     * @param string $class Class string of the child struct.
     * @phpstan-param class-string $class
     * @param int $dimension Example: 0 = Struct[], 1 = Struct[][], etc...
     */
    public function __construct(
        string $class,
        int $dimension = 0
    )
    {
    }

}