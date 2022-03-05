<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseError;

final class ChildObjectContext extends BasePropertyContext
{

    protected object $object;

    /**
     * @var ParseError|null If one is thrown by {@link ObjectContext::copyToNewObject()} in {@link ChildObjectContext::__construct}.
     */
    protected ?ParseError $error = null;

    public function __construct(
        PropertyDetails         $details,
        protected ObjectContext $objectContext
    )
    {
        try {
            $this->object = $this->asObjectContext()->copyToNewObject(
                "object array"
            );
        } catch (ParseError $err) {
            $this->error = $err;
        }

        parent::__construct(
            $details
        );
    }

    /**
     * @return object Copy the object context to a new object.
     */
    public function getValue() : object
    {
        return $this->object;
    }

    /**
     * @return ObjectContext
     */
    public function asObjectContext() : ObjectContext
    {
        return $this->objectContext;
    }

    public function getErrorsTree() : array
    {
        return $this->error->getErrorsTree();
    }

}