<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Endermanbugzjfc\ConfigStruct\ListType;
use ReflectionException;

/**
 * @see ListType::__construct()
 */
final class InvalidListTypeError extends BaseParseError
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