<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use Error;
use Throwable;
use const E_RECOVERABLE_ERROR;

/**
 * This error shouldn't be caught. Direct change to the source code (structure of the struct class) should be made.
 */
final class StructureError extends Error
{

    public function __construct(
        string     $message = "",
        ?Throwable $previous = null,
        int $code = E_RECOVERABLE_ERROR
    )
    {
        parent::__construct($message, $code, $previous);
    }

}