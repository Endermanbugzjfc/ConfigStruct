<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

abstract class BasePropertyContext
{
    abstract public function getValue() : mixed;

    public function __construct(
        private PropertyDetails $details
    ) {
    }


    final public function getDetails() : PropertyDetails
    {
        return $this->details;
    }

    /**
     * @return array<string, mixed> Key = header label.
     */
    public function getErrorsTree() : array
    {
        return [];
    }

    public function hasError() : bool
    {
        $tree = $this->getErrorsTree();
        return $tree !== [];
    }

    final public function getErrorsTreeKey() : string
    {
        return "element \"{$this->getDetails()->getKeyName()}\"";
    }

    /**
     * Get the errors tree and put it in an array using the key name ({@link BasePropertyContext::getErrorsTreeKey()}) as key.
     * @return array<string, mixed[]> Can be array_merge() with the wrapped errors tree of other properties.
     */
    final public function getWrappedErrorsTree() : array
    {
        $key = $this->getErrorsTreeKey();
        $tree = $this->getErrorsTree();
        return $tree === []
            ? []
            : [
                $key => $tree
            ];
    }

    /**
     * @return mixed[]
     */
    public function getUnhandledElements() : array
    {
        return [];
    }

    public function omitCopyToObject() : bool
    {
        return false;
    }
}