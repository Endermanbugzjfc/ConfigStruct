<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Stringable;
use Throwable;

abstract class BaseParseError implements Stringable
{

    abstract public function getMessage();

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