<?php

#[Attribute(Attribute::TARGET_PROPERTY)] class KeyName
{

    public function __construct(
        string $name
    )
    {
    }

}