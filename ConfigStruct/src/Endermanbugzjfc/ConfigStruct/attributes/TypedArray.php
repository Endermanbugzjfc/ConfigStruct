<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use ArrayAccess;
use Attribute;
use Iterator;

#[Attribute(Attribute::TARGET_PROPERTY)] class TypedArray
{

    /**
     * A {@link TypedArray} refer to an array of child structs.
     *
     * Property that uses this attribute should has the type array or classes which implement the {@link ArrayAccess} and {@link Iterator} interface.
     *
     * @param string $class Class string of the child struct.
     * @phpstan-param class-string $class
     * @param int $wrapping How many [] should be wrapping the child struct, count from zero. Example: 0 = Struct[], 1 = Struct[][], etc...
     */
    public function __construct(
        string $class,
        int    $wrapping = 0
    )
    {
    }

}