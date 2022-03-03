<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use ReflectionProperty;

final class ChildObjectContext extends PropertyDefaultContext
{

    /**
     * @inheritDoc
     */
    public function __construct(
        string                      $keyName,
        ReflectionProperty          $reflection,
        array                       $errors,
        protected ObjectContext $objectParseOutput
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
     * @return ObjectContext
     */
    public function getObjectParseOutput() : ObjectContext
    {
        return $this->objectParseOutput;
    }

}