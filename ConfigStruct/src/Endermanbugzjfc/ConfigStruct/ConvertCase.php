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
    public const PASCAL_CASE = [self::class, "pascalCase"];
    public const CAMEL_CASE = [self::class, "camelCase"];
    public const SNAKE_CASE = [self::class, "snakeCase"];
    public const SCREAMING_SNAKE_CASE = [self::class, "screamingSnakeCase"];
    public const KEBAB_CASE = [self::class, "kebabCase"];
    public const SCREAMING_KEBAB_CASE = [self::class, "screamingKebabCase"];
    public const DOT_CASE = [self::class, "dotCase"];
    public const SCREAMING_DOT_CASE = [self::class, "screamingDotCase"];

    public function __construct(
        callable $caseConverter
    )
    {
    }

    /**
     * lowercase
     * @param string $name
     * @return string
     */
    public static function lowercase(
        string $name
    ) : string {
        return strtolower($name);
    }

    /**
     * UPPERCASE
     * @param string $name
     * @return string
     */
    public static function uppercase(
        string $name
    ) : string {
        return strtoupper($name);
    }

    /**
     * PascalCase
     * @param string $name
     * @return string
     */
    public static function pascalCase(
        string $name
    ) : string {
    }

    /**
     * camelCase
     * @param string $name
     * @return string
     */
    public static function camelCase(
        string $name
    ) : string {

    }

    /**
     * snake_case
     * @param string $name
     * @return string
     */
    public static function snakeCase(
        string $name
    ) : string {

    }

    /**
     * SCREAMING_SNAKE_CASE
     * @param string $name
     * @return string
     */
    public static function screamingSnakeCase(
        string $name
    ) : string {

    }

    /**
     * kebab-case
     * @param string $name
     * @return string
     */
    public static function kebabCase(
        string $name
    ) : string {

    }

    /**
     * SCREAMING-KEBAB-CASE
     * @param string $name
     * @return string
     */
    public static function screamingKebabCase(
        string $name
    ) : string {

    }

    /**
     * dot.case
     * @param string $name
     * @return string
     */
    public static function dotCase(
        string $name
    ) : string {

    }

    /**
     * SCREAMING.DOT.CASE
     * @param string $name
     * @return string
     */
    public static function screamingDotCase(
        string $name
    ) : string {

    }

}