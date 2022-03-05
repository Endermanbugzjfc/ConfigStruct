<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseContext\BeforeParse\PropertyDetails;
use Throwable;

abstract class BasePropertyContext
{
    abstract public function getValue() : mixed;

    public function __construct(
        private PropertyDetails $details
    )
    {
    }

    /**
     * @return PropertyDetails
     */
    final public function getDetails() : PropertyDetails
    {
        return $this->details;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors() : array
    {
        return [];
    }

}