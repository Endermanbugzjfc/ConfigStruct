<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseError;
use Endermanbugzjfc\ConfigStruct\Utils\ConfigStructUtils;
use function array_merge;

final class ListContext extends BasePropertyContext
{

    /**
     * @param PropertyDetails $details
     * @param ObjectContext[] $objectContexts
     * @param ParseError[] $errors Typically {@link InvalidListTypeError}.
     */
    public function __construct(
        PropertyDetails $details,
        protected array $objectContexts,
        protected array $errors
    )
    {
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
        foreach (
            $this->getObjectContextsArray()
            as $key => $output
        ) {
            $return[$key] = $output->copyToNewObject();
        }
        return $return ?? [];
    }

    /**
     * @return ObjectContext[] Keys are reserved.
     */
    public function getObjectContextsArray() : array
    {
        return $this->objectContexts;
    }

    /**
     * @return bool False = indexed array (incremental numeric keys). True = associative array (disordered keys).
     */
    public function isAssociative() : bool
    {
        return !ConfigStructUtils::arrayIsList(
            $this->getObjectContextsArray()
        );
    }

    public function getErrorsTree() : array
    {
        $tree = parent::getErrorsTree();
        $tree = array_merge(
            $tree,
            $this->errors
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
        string $elementKey
    ) : string {
        return "element \"$elementKey\"";
    }

}