<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    /**
     * @inheritDoc
     */
    protected function __construct(
        ReflectionProperty $reflection,
        string             $keyName,
        ObjectParseOutput $output
    )
    {
        parent::__construct(
            $reflection,
            $keyName,
            $output
        );
    }

    /**
     * @inheritDoc
     */
    public static function create(
        ReflectionProperty $reflection,
        string             $keyName,
        mixed              $output
    ) : PropertyParseOutput
    {
        return new self(
            $reflection,
            $keyName,
            $output
        );
    }

    /**
     * @return ObjectParseOutput
     */
    public function getChildStruct() : ObjectParseOutput
    {
        return $this->output;
    }

    /**
     * @return object A new instance of the child struct which contains the parsed value.
     */
    protected function getFlattenedValue() : object
    {
        return $this->getChildStruct()->copyValuesToNewObject();
    }

}