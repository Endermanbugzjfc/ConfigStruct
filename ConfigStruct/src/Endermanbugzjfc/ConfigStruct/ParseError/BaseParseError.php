<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use Stringable;
use Throwable;

/**
 * A different error system from PHP {@link Throwable} which is used in parse errors tree. And is wrapped in a {@link ParseErrorsWrapper} once being thrown.
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
    ) {
    }

    public function getPrevious() : ?Throwable
    {
        return $this->previous;
    }

    public function __debugInfo() : ?array
    {
        $return = [
            "message" => $this->getMessage()
        ];
        $previous = $this->getPrevious();
        if ($previous !== null) {
            $return["previous"] = [
                "class" => $previous::class,
                "message" => $previous->getMessage(),
                "code" => $previous->getCode()
            ];
        }

        return $return;
    }
}