<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Throwable;

final class RawContext extends BasePropertyContext
{
    use NonAbstractContextTrait;

    protected mixed $value;

    /**
     * @param BasePropertyContext $context
     * @param mixed $value
     * @param Throwable[] $errors
     * @return static
     */
    public static function create(
        BasePropertyContext $context,
        mixed               $value,
        array               $errors
    ) : self
    {
        $self = new self();
        $self->substitute($context);
        $self->value = $value;
        $self->errors = $errors;

        return $self;
    }

    /**
     * Return the raw value without any special logics.
     * @return mixed
     */
    public function getValue() : mixed
    {
        return $this->value;
    }

    /**
     * @return Throwable[]
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

}