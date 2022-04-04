<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

final class RawContext extends BasePropertyContext
{

    /**
     * @param mixed $value
     */
    public function __construct(
        PropertyDetails $details,
        protected mixed $value
    ) {
        parent::__construct(
            $details
        );
    }

    /**
     * Return the raw value without any special logics.
     */
    public function getValue() : mixed
    {
        return $this->value;
    }
}