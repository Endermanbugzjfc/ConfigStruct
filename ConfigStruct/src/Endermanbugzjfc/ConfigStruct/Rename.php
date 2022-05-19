<?php

declare(strict_types=1);

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rename
{
    public function __construct(
        string $name,
        array $moreNames,
        bool $omitConvertCase
    ) {
    }
}