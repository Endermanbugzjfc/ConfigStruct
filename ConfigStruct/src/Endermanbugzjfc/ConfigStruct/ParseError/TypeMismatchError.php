<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use TypeError;
use function implode;

final class TypeMismatchError extends BaseParseError
{

    /**
     * @param TypeError $previous
     * @param string[] $expectedTypes
     * @param string $givenType
     */
    public function __construct(
        TypeError        $previous,
        protected array  $expectedTypes,
        protected string $givenType
    )
    {
        parent::__construct(
            $previous
        );
    }

    /**
     * @return string[]
     */
    public function getExpectedTypes() : array
    {
        return $this->expectedTypes;
    }

    /**
     * @return string
     */
    public function getGivenType() : string
    {
        return $this->givenType;
    }

    public function getMessage() : string
    {
        $expectedTypes = implode(
            " / ",
            $this->getExpectedTypes()
        );
        return "Element is a {$this->getGivenType()} while it should be a $expectedTypes";
    }
}