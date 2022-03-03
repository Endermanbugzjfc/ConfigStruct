<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

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

}