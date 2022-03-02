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
            $reflection,
            $keyName
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