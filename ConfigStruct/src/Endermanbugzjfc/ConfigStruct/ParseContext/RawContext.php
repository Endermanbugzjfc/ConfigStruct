<?php

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

final class RawContext extends BasePropertyContext
{
    use NonAbstractContextTrait;

    protected mixed $value;

    public static function create(
        BasePropertyContext $context,
        mixed $value
    ) : self
    {
        $self = new self();
        $self->substitute($context);
        $self->value = $value;

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

}