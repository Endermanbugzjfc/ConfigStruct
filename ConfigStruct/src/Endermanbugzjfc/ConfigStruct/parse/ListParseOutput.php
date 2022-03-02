<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ListParseOutput extends PropertyParseOutput
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
}