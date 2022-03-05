<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Endermanbugzjfc\ConfigStruct\ParseError;
use Stringable;
use Throwable;

/**
 * A different error system from PHP {@link Throwable} which is used in parse errors tree. And is wrapped in a {@link ParseError} once being thrown.
 */
abstract class BaseParseError implements Stringable
{

    abstract public function getMessage() : string;

    public function __toString() : string
    {
        return $this->getMessage();
    }

    public function __construct(
        protected ?Throwable $previous = null
    )
    {
    }

    public function getPrevious() : ?Throwable
    {
        return $this->previous;
    }

}