<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use function array_merge;

final class ListContext extends BasePropertyContext
{

    /**
     * @var object[] Keys are reserved.
     */
    protected array $objects;

    /**
     * @var array<string, array|BaseParseError>
     */
    protected array $baseErrorsTree = [];

    /**
     * @param ObjectContext[] $objectContexts
     * @param array $unhandledElements
     */
    public function __construct(
        PropertyDetails $details,
        protected array $objectContexts,
        array           $elementsErrorsTree,
        protected array $unhandledElements
    ) {
        foreach ($elementsErrorsTree as $key => $value) {
            $subElementKey = self::getErrorsTreeSubElementKey(
                $key
            );
            $this->baseErrorsTree[$subElementKey] = $value;
        }

        $contexts = $this->getObjectContextsArray();
        foreach ($contexts as $key => $context) {
            try {
                $object = $context->copyToNewObject(
                    "object array"
                );
            } catch (ParseErrorsWrapper $err) {
                $subElementKey = self::getErrorsTreeSubElementKey(
                    $key
                );
                $this->baseErrorsTree[$subElementKey][] = $err->getErrorsTree();
                continue;
            }
            $objects[$key] = $object;
        }
        $this->objects = $objects ?? [];

        parent::__construct(
            $details
        );
    }

    /**
     * Copy object contexts to new objects.
     * @return object[] Keys are reserved.
     */
    public function getValue() : array
    {
        return $this->objects;
    }

    /**
     * @return ObjectContext[] Keys are reserved.
     */
    public function getObjectContextsArray() : array
    {
        return $this->objectContexts;
    }

    public function getErrorsTree() : array
    {
        $tree = parent::getErrorsTree();
        $tree = array_merge(
            $tree,
            $this->baseErrorsTree
        );

        $contexts = $this->getObjectContextsArray();
        foreach ($contexts as $key => $context) {
            if ($context->hasError()) {
                $treeKey = self::getErrorsTreeSubElementKey(
                    $key
                );
                $tree[$treeKey] = $context->getErrorsTree();
            }
        }

        return $tree;
    }

    private static function getErrorsTreeSubElementKey(
        string|int $elementKey
    ) : string {
        return "index \"$elementKey\"";
    }


    public function getUnhandledElements() : array
    {
        return $this->unhandledElements;
    }
}