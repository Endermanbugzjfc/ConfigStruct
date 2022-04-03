<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use TypeError;
use function implode;

/**
 * When a {@link TypeError} occurs in {@link ObjectContext::copyToObject()}.
 */
final class TypeMismatchError extends BaseParseError
{

    /**
     * @param string[] $expectedTypes
     * @param string $givenType
     */
    public function __construct(
        TypeError        $previous,
        protected array  $expectedTypes,
        protected string $givenType
    ) {
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
        return "Element is {$this->getGivenType()} while it should be $expectedTypes";
    }
}