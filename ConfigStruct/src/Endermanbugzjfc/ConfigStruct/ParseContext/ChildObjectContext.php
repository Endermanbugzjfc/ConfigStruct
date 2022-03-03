<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Throwable;
use function array_merge;

final class ChildObjectContext extends BasePropertyContext
{
    use NonAbstractContextTrait;

    protected ObjectContext $objectContext;

    /**
     * @param BasePropertyContext $context
     * @param ObjectContext $objectContext
     * @return static
     */
    public static function create(
        BasePropertyContext $context,
        ObjectContext       $objectContext
    ) : self
    {
        $self = new self();
        $self->substitute($context);
        $self->objectContext = $objectContext;

        return $self;
    }

    /**
     * @return object Copy the object context to a new object.
     */
    public function getValue() : object
    {
        return $this->asObjectContext()->copyToNewObject();
    }

    /**
     * @return ObjectContext
     */
    public function asObjectContext() : ObjectContext
    {
        return $this->objectContext;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors() : array
    {
        $errs = [];
        $properties = $this->asObjectContext()->getErrorProperties();
        foreach ($properties as $property) {
            $propertyErrs = $property->getErrors();
            $errs = array_merge(
                $errs,
                $propertyErrs
            );
        }

        return $errs;
    }

}