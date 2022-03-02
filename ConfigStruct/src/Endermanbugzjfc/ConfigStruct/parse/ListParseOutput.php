<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\parse;

use ArrayAccess;
use Iterator;
use ReflectionProperty;

final class ListParseOutput extends PropertyParseOutput implements ArrayAccess, Iterator
{

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     * @param ChildStructParseOutput[] $childStructParseOutput
     */
    public function __construct(
        string             $keyName,
        ReflectionProperty $reflection,
        protected array    $childStructParseOutput
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

    public function current()
    {
        // TODO: Implement current() method.
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

    public function valid()
    {
        // TODO: Implement valid() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    public function offsetExists(mixed $offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet(mixed $offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet(
        mixed $offset,
        mixed $value
    )
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}