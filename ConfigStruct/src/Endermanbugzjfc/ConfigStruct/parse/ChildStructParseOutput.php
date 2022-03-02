<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    public function __construct(
        string                      $keyName,
        ReflectionProperty          $reflection,
        protected ObjectParseOutput $objectParseOutput
    )
    {
        parent::__construct(
            $keyName,
            $reflection
        );
    }

    public function getValue() : bool|int|float|string|array
    {
        return $this->getObjectParseOutput()->copyToNewObject();
    }

    /**
     * @return ObjectParseOutput
     */
    public function getObjectParseOutput() : ObjectParseOutput
    {
        return $this->objectParseOutput;
    }

}