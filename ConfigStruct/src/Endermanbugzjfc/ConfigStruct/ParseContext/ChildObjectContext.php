<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

final class ChildObjectContext extends BasePropertyContext
{
    use ExtendingContextTrait;

    protected ObjectContext $objectContext;

    public static function create(
        BasePropertyContext $context,
        ObjectContext       $objectContext
    ) : self
    {
        $return = self::createFromDefaultContext(
            $context
        );
        $return->objectContext = $objectContext;

        return $return;
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