<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\Utils\ConfigStructUtils;
use ReflectionException;
use Throwable;

final class ListContext extends BasePropertyContext
{
    use NonAbstractContextTrait;

    /**
     * @var ObjectContext[]
     */
    protected array $objectContexts;

    /**
     * @var Throwable[]
     */
    protected array $errors;

    /**
     * @param BasePropertyContext $context
     * @param ObjectContext[] $objectContexts
     * @param Throwable[] $errors No key. Mostly {@link ReflectionException} from invalid {@link ListType} attributes.
     * @return self
     */
    public static function create(
        BasePropertyContext $context,
        array               $objectContexts,
        array               $errors
    ) : self
    {
        $self = new self();
        $self->substitute($context);
        $self->objectContexts = $objectContexts;
        $self->errors = $errors;

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
        return !ConfigStructUtils::arrayIsList(
            $this->getObjectContextsArray()
        );
    }

    /**
     * @return Throwable[] No key. Mostly {@link ReflectionException} from invalid {@link ListType} attributes.
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

}