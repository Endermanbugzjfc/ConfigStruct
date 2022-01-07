<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;


use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)] class CreateDefaultValue
{

    /**
     * @phpstan-param class-string $class
     */
    public function __construct(?string $class = null)
    {
    }

}