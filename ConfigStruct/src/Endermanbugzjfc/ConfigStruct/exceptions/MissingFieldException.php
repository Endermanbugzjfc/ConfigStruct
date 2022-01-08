<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use ReflectionProperty;

class MissingFieldException extends ConfigStructException
{

    public function __construct(
        protected ReflectionProperty $field,
        protected ?string            $configFile = null
    )
    {
        parent::__construct(
            "Required field \"$field\" missing"
            . (isset($this->configFile) ? " in $this->configFile" : ""),
        );
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