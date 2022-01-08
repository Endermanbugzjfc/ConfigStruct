<?php

namespace Endermanbugzjfc\ConfigStruct\utils;

use Exception;
use ReflectionProperty;

class MissingFieldException extends Exception
{

    public function __construct(
        protected ReflectionProperty $field,
        protected ?string            $configFile = null
    )
    {
    }

    /**
     * @return ReflectionProperty
     */
    public function getField() : ReflectionProperty
    {
        return $this->field;
    }

    /**
     * @return string|null
     */
    public function getConfigFile() : ?string
    {
        return $this->configFile;
    }


}