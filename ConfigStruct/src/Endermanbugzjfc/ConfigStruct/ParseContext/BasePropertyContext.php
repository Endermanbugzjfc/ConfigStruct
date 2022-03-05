<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseContext\BeforeParse\PropertyDetails;
use function array_walk_recursive;

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
     * @return int
     */
    public function getErrorsCount() : int
    {
        $count = 0;
        $tree = $this->getErrorsTree();
        array_walk_recursive(
            $tree,
            function () use
            (
                &
                $count
            ) {
                $count++;
            }
        );

        return $count;
    }

    /**
     * Get the errors tree and put it in an array using the key name (not exact) as key.
     * @return array array<string, array> Can be array_merge() with the wrapped errors tree of other properties.
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