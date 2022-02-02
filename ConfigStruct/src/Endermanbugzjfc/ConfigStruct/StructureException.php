<?php

namespace Endermanbugzjfc\ConfigStruct;

use RuntimeException;
use Throwable;
use const E_ERROR;

/**
 * This exception should neither be recovered nor be caught. Direct change to the source code (structure of the struct class) should be made.
 */
final class StructureException extends RuntimeException
{

    public function __construct(
        string     $message = "",
        ?Throwable $previous = null,
        int        $code = E_ERROR
    )
    {
        parent::__construct($message, $code, $previous);
    }

}