<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class RawParseOutput extends PropertyParseOutput
{

    public function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        protected mixed    $value
    )
    {
        parent::__construct(
            $keyName,
            $reflection
        );
    }

    /**
     * @return mixed
     */
    public function getValue() : mixed
    {
        return $this->value;
    }

}