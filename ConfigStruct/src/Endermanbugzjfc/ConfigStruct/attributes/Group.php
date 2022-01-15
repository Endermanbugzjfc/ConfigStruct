<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use ArrayAccess;
use Attribute;
use Iterator;

#[Attribute(Attribute::TARGET_PROPERTY)] class Group
{

    /**
     * A {@link Group} refer to an array of child structs.
     *
     * Property that uses this attribute should has the type array or classes which implement the {@link ArrayAccess} and {@link Iterator} interface.
     *
     * @param string $class Class string of the child struct.
     * @phpstan-param class-string $class
     * @param int $wrapping How many [] should be wrapping the child struct, count from zero. Example: 0 = Struct[], 1 = Struct[][], etc...
     * @param string ...$defaultValues Class name of structs that will be appended to the group as default value during initialize. Structure should be the same in the above struct and all default-value structs.
     * @phpstan-param class-string $defaultValues
     */
    public function __construct(
        string $class,
        int    $wrapping = 0,
        string ...$defaultValues
    )
    {
    }

}