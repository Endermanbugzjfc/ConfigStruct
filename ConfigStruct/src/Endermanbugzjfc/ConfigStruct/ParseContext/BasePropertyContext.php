<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseContext\BeforeParse\PropertyDetails;
use function array_values;
use function count;
use function is_array;

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

    private static function getErrorsCountTreeRecursive(
        array $tree
    ) : array
    {
        foreach ($tree as $key => $item) {
            if (!is_array(
                $item
            )) {
                continue;
            }

            if (array_values(
                    $item
                ) === $item) {
                $tree[$key] = count($item);
                continue;
            }

            $tree[$key] = self::getErrorsCountTreeRecursive(
                $item
            );
        }

        return $tree;
    }

    private function getErrorsTreeKey() : string
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

}