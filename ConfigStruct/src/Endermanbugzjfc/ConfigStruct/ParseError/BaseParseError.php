<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Throwable;

abstract class BaseParseError
{

    abstract public function getMessage();

    public function getPrevious() : ?Throwable {
        return null;
    }

}