<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use Endermanbugzjfc\ConfigStruct\Analyse;
use Error;

/**
 * This error is thrown by {@link Analyse} when the structure of a struct class is invalid.
 *
 * This error shouldn't be caught. Direct change to the source code (structure of the struct class) should be made.
 */
final class StructureError extends Error
{

}