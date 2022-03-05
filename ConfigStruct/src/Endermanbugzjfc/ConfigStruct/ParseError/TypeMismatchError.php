<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Throwable;

final class TypeMismatchError extends BaseParseError
{

    public function __construct(
        ?Throwable $previous = null
    )
    {
        parent::__construct(
            $previous
        );
    }

    public function getMessage() : string
    {
        // TODO: Implement getMessage() method.
    }
}