<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\StructureError;

/**
 * This attribute should be applied on a child struct and not the root struct.
 *
 * Recursion check will not be performed on structs with this attribute.
 * The initialization of that child struct will stop silently without throwing an {@link StructureError}.
 */
#[Attribute(Attribute::TARGET_CLASS)] class Recursive
{

}