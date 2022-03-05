<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use ReflectionException;

class InvalidListTypeError extends BaseParseError
{

    public function __construct(
        ReflectionException $previous,
        protected string    $type,
    )
    {
        parent::__construct(
            $previous
        );
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    public function getMessage() : string
    {
        return "Invalid list type \"{$this->getType()}\"";
    }
}