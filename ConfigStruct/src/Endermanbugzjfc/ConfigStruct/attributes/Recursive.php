<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;

/**
 * Recursive child struct check will not be performed on classes with this attribute.
 *
 * The initialization of child struct will also stop silently without throwing a {@link StructureException}.
 */
#[Attribute(Attribute::TARGET_CLASS)] class Recursive
{

    public function __construct()
    {
    }

}