<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\StructureException;
use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    /**
     * @param ReflectionProperty $reflection
     * @param string $keyName
     * @param mixed $output
     * @param StructureException[] $exceptions
     */
    protected function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        StructParseOutput  $output,
        array              $exceptions
    )
    {
        parent::__construct(
            $reflection,
            $keyName,
            $output,
            $exceptions
        );
    }

    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output,
        array              $exceptions
    ) : PropertyParseOutput
    {
        return new self(
            $reflection,
            $keyName,
            $output,
            $exceptions
        );
    }

    /**
     * @return StructParseOutput
     */
    public function getChildStruct() : StructParseOutput
    {
        return $this->output;
    }

    protected function getFlattenedValue() : object
    {
        return $this->getChildStruct()->copyValuesToNewObject();
    }

}