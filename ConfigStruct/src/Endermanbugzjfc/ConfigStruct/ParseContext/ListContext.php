<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use function array_values;

final class ListContext extends BasePropertyContext
{
    use NonAbstractContextTrait;

    /**
     * @var ObjectContext[]
     */
    protected array $objectContexts;

    /**
     * @param BasePropertyContext $context
     * @param ObjectContext[] $objectContexts
     * @return self
     */
    public static function create(
        BasePropertyContext $context,
        array               $objectContexts
    ) : self
    {
        $self = new self();
        $self->substitute($context);
        $self->objectContexts = $objectContexts;

        return $self;
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
        return array_values(
                $this->getObjectContextsArray()
            ) === $this->getObjectContextsArray();
    }

}