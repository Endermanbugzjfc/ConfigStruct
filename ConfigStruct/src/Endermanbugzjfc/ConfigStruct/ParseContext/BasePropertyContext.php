<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

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
     * @return array
     */
    public function getErrorsTree() : array
    {
        return [];
    }

    public function hasError() : bool
    {
        $tree = $this->getErrorsTree();
        return !empty(
        $tree
        );
    }

    final public function getErrorsTreeKey() : string
    {
        return "element \"{$this->getDetails()->getKeyName()}\"";
    }

    /**
     * Get the errors tree and put it in an array using the key name ({@link BasePropertyContext::getErrorsTreeKey()}) as key.
     * @return array array<string, array> Can be array_merge() with the wrapped errors tree of other properties.
     */
    final public function getWrappedErrorsTree() : array
    {
        $key = $this->getErrorsTreeKey();
        $tree = $this->getErrorsTree();
        return [
            $key => $tree
        ];
    }

    /**
     * Example:
     * Elements tree: [a => [b, c], d]
     * [a => [b], d] marks a.b and d as unhandled elements. Omitting a.c.
     * @return array
     */
    public function getUnhandledElementKeyNamesTree() : array
    {
        return [];
    }

}