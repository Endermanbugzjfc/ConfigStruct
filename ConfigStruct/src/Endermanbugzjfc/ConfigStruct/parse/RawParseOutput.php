<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class RawParseOutput extends PropertyParseOutput
{

    /**
     * @inheritDoc
     */
    public function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        array              $errors,
        protected mixed    $value
    )
    {
        parent::__construct(
            $keyName,
            $reflection,
            $errors
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