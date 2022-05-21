<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;

final class ChildObjectContext extends BasePropertyContext
{
    protected object $object;

    protected ObjectContext $objectContext;

    /**
     * @var ParseErrorsWrapper|null If one is thrown by {@link ObjectContext::copyToNewObject()} in {@link ChildObjectContext::__construct}.
     */
    protected ?ParseErrorsWrapper $error = null;

    public function __construct(
        PropertyDetails                  $details,
        ObjectContext|ParseErrorsWrapper $objectContextOrError
    ) {
        parent::__construct(
            $details
        );
        if ($objectContextOrError instanceof ParseErrorsWrapper) {
            $this->error = $objectContextOrError;
            return;
        }

        $this->objectContext = $objectContextOrError;
        try {
            $this->object = $this->asObjectContext()->copyToNewObject(
                "object array"
            );
        } catch (ParseErrorsWrapper $err) {
            $this->error = $err;
        }
    }

    /**
     * @return object Copy the object context to a new object.
     */
    public function getValue() : object
    {
        return $this->object;
    }


    public function asObjectContext() : ObjectContext
    {
        return $this->objectContext;
    }

    public function getErrorsTree() : array
    {
        return $this->error?->getErrorsTree() ?? [];
    }

    /**
     * @return mixed[] Unhandled elements in the object context.
     */
    public function getUnhandledElements() : array
    {
        return $this->asObjectContext()->getUnhandledElements();
    }

    public function omitCopyToObject() : bool
    {
        return !isset(
            $this->object
        );
    }
}