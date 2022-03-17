<?php

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;
use function strtolower;
use function strtoupper;

/**
 * Walk selected key names in a class with the case converter when emitting or parsing.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ConvertCase
{

    public const LOWERCASE = [self::class, "lowercase"];
    public const UPPERCASE = [self::class, "uppercase"];

    public function __construct(
        callable $caseConverter
    )
    {
    }

    /**
     * lowercase.
     * @param string $name
     * @return string
     */
    public function lowercase(
        string $name
    ) : string {
        return strtolower($name);
    }

    /**
     * UPPERCASE.
     * @param string $name
     * @return string
     */
    public function uppercase(
        string $name
    ) : string {
        return strtoupper($name);
    }

}