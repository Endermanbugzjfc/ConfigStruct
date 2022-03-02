<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class RawParseOutput extends PropertyParseOutput
{

    public function __construct(
        string                                $keyName,
        ReflectionProperty                    $reflection,
        protected bool|int|float|string|array $value
    )
    {
        parent::__construct(
            $keyName,
            $reflection
        );
    }

    /**
     * @return array|bool|float|int|string
     */
    public function getValue() : float|array|bool|int|string
    {
        return $this->value;
    }

}