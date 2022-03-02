<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use ReflectionProperty;

final class ChildStructParseOutput extends PropertyParseOutput
{

    /**
     * @inheritDoc
     */
    public function __construct(
        string                      $keyName,
        ReflectionProperty          $reflection,
        array                       $errors,
        protected ObjectParseOutput $objectParseOutput
    )
    {
        parent::__construct(
            $keyName,
            $reflection,
            $errors
        );
    }

    public function getValue() : object
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