<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Throwable;

abstract class BaseParseError
{

    abstract public function getMessage();

    public function __construct(
        protected ?Throwable $previous = null
    )
    {
    }

    public function getPrevious() : ?Throwable {
        return $this->previous;
    }

}