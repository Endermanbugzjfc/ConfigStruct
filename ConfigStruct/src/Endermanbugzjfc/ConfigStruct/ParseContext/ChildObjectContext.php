<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

final class ChildObjectContext extends BasePropertyContext
{

    public function __construct(
        PropertyDetails         $details,
        protected ObjectContext $objectContext
    )
    {
        parent::__construct(
            $details
        );
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