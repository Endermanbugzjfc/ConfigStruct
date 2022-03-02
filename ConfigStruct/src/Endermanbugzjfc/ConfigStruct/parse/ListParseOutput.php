<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\parse;

use ArrayAccess;
use Iterator;
use ReflectionProperty;
use function array_values;

final class ListParseOutput extends PropertyParseOutput implements ArrayAccess, Iterator
{

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     * @param ObjectParseOutput[] $objectParseOutput
     */
    public function __construct(
        string             $keyName,
        ReflectionProperty $reflection,
        protected array    $objectParseOutput
    )
    {
        parent::__construct(
            $keyName,
            $reflection
        );
    }

    public function getValue() : bool|int|float|string|array
    {
        // TODO: Implement getValue() method.
    }

    /**
     * @return ObjectParseOutput[]
     */
    public function getObjectParseOutput() : array
    {
        return $this->objectParseOutput;
    }

    public function isAssociative() : bool
    {
        return array_values(
            $this->objectParseOutput
        ) === $this->objectParseOutput;
    }

}