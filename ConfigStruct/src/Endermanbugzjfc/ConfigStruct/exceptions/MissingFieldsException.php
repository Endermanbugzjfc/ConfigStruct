<?php

namespace Endermanbugzjfc\ConfigStruct\exceptions;

use ReflectionProperty;
use function array_map;
use function count;
use function implode;

class MissingFieldsException extends SetupException
{

    /**
     * @param array<int|string> $fields Missing fields
     * @param ReflectionProperty[] $properties The corresponding properties of the missing fields
     */
    public function __construct(
        protected array  $fields,
        protected array  $properties,
        protected string $configFile
    )
    {
        $this->updateMessage();
    }

    /**
     * @return array<int|string>
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * @return ReflectionProperty[]
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getConfigFile() : string
    {
        return $this->configFile;
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile(string $configFile) : void
    {
        $this->configFile = $configFile;
        $this->updateMessage();
    }

    /**
     * @return void Updates the exception message when its parameters changed.
     */
    protected function updateMessage() : void
    {
        $s = count($this->getFields()) > 1;
        $fields = implode(", ", array_map(function (string $field) {
            return "\"$field\"";
        }, $this->getFields()));
        parent::__construct(
            "Required field$s $fields missing"
            . ($this->getConfigFile() !== null
                ? " in {$this->getConfigFile()}"
                : ""
            ),
        );
    }

}