<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use ReflectionProperty;
use function array_map;
use function count;
use function implode;

class MissingFieldsException extends SetupException
{

    public function __construct(
        protected array   $field,
        protected ?string $configFile = null
    )
    {

        $s = count($this->field) > 1;
        $fields = implode(", ", array_map(function (string $field) {
            return "\"$field\"";
        }, $this->field));
        parent::__construct(
            "Required field$s $fields missing"
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