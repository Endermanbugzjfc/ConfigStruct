<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use Endermanbugzjfc\ConfigStruct\Analyse;
use Exception;

/**
 * This exception is thrown by {@link Analyse} when the structure of a struct class is invalid.
 *
 * This exception shouldn't be caught. Direct change to the source code (structure of the struct class) should be made.
 */
class StructureException extends Exception
{

}