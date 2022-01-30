<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class PropertyParseOutput extends ParseOutput
{

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     */
    private function __construct(
        protected ReflectionProperty $reflection,
        protected string             $keyName,
        protected mixed              $output
    )
    {
    }

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     * @return PropertyParseOutput
     */
    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output
    ) : self
    {
        return new self(
            $reflection,
            $keyName,
            $output
        );
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return string
     */
    public function getKeyName() : string
    {
        return $this->keyName;
    }

    /**
     * @return mixed
     */
    public function getOutput() : mixed
    {
        return $this->output;
    }

    protected function getFlattenedValue() : mixed
    {
        return $this->output instanceof ParseOutput
            ? $this->output->getFlattenedValue()
            : $this->output;
    }

}