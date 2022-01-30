<?php

namespace Endermanbugzjfc\ConfigStruct;

use PHPUnit\Framework\Exception;
use Throwable;
use const E_RECOVERABLE_ERROR;

/**
 * This exception should neither be recovered nor be caught. Direct change to the source code (structure of the struct class) should be made.
 */
final class StructureException extends Exception
{

    public function __construct(
        string     $message = "",
        ?Throwable $previous = null,
        int        $code = E_RECOVERABLE_ERROR
    )
    {
        parent::__construct($message, $code, $previous);
    }

}