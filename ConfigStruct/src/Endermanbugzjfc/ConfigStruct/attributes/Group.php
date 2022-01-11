<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] class Group
{

    /**
     * A {@link Group} refer to an array of child structs.
     * @param string $class Class string of the child struct.
     * @phpstan-param class-string $class
     * @param int $wrapping The many [] should be wrapping the child struct, count from zero. Example: 0 = Struct[], 1 = Struct[][], etc...
     */
    public function __construct(
        string $class,
        int    $wrapping = 0
    )
    {
    }

}