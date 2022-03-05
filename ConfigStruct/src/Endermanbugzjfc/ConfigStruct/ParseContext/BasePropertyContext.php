<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseContext\BeforeParse\PropertyDetails;

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
     * @return array Only string keys will be shown.
     */
    public function getErrorsTree() : array
    {
        return [];
    }

}