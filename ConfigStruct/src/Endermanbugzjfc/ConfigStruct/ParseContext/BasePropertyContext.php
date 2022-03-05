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

    /**
     * Get the errors tree and put it in an array using the key name (not exact) as key.
     * @return array array<string, array>
     */
    final public function getWrappedErrorsTree() : array
    {
        $key = "element \"{$this->getDetails()->getKeyName()}\"";
        $tree = $this->getErrorsTree();
        return [
            $key => $tree
        ];
    }

}