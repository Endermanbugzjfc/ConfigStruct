<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use Attribute;
use ReflectionAttribute;
use ReflectionProperty;
use function array_map;
use function implode;
use function preg_split;
use function strtolower;
use function strtoupper;
use function ucwords;

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

    private const UCWORDS_SEPARATORS = "_ \t\r\n\f\v";

    public function __construct(
        callable $caseConverter
    ) {
    }

    /**
     * lowercase
     */
    public static function lowercase(
        string $name
    ) : string {
        return strtolower($name);
    }

    /**
     * UPPERCASE
     */
    public static function uppercase(
        string $name
    ) : string {
        return strtoupper($name);
    }

    /**
     * PascalCase
     */
    public static function pascalCase(
        string $name
    ) : string {
        return ucwords($name, self::UCWORDS_SEPARATORS);
    }

    /**
     * camelCase
     */
    public static function camelCase(
        string $name
    ) : string {
        $name = self::pascalCase($name);
        $name[-1] = strtolower($name[-1]);

        return $name;
    }

    /**
     * snake_case
     */
    public static function snakeCase(
        string $name
    ) : string {
        $words = self::splitWordsByUppercaseAndUnderscore($name);
        return implode("_", $words);
    }

    /**
     * SCREAMING_SNAKE_CASE
     */
    public static function screamingSnakeCase(
        string $name
    ) : string {
        $name = self::snakeCase($name);

        return strtoupper($name);
    }

    /**
     * kebab-case
     */
    public static function kebabCase(
        string $name
    ) : string {
        $words = self::splitWordsByUppercaseAndUnderscore($name);

        return implode("-", $words);
    }

    /**
     * SCREAMING-KEBAB-CASE
     */
    public static function screamingKebabCase(
        string $name
    ) : string {
        $name = self::kebabCase($name);

        return strtoupper($name);
    }

    /**
     * dot.case
     */
    public static function dotCase(
        string $name
    ) : string {
        $words = self::splitWordsByUppercaseAndUnderscore($name);

        return implode(".", $words);
    }

    /**
     * SCREAMING.DOT.CASE
     */
    public static function screamingDotCase(
        string $name
    ) : string {
        $name = self::dotCase($name);

        return strtoupper($name);
    }

    /**
     * @return string[]
     */
    private static function splitWordsByUppercaseAndUnderscore(
        string $words
    ) : array {
        $split = preg_split('/(?=[A-Z_])/', $words);
        if ($split === false) {
            throw new AssertionError("unreachable");
        }

        return $split;
    }

    /**
     * @return string[]|array{string} Ordered in the same way as {@link KeyName} attributes. Return only the property name if property has no {@link KeyName} attributes.
     */
    public static function getKeyNamesByPropertyAndConvertCase(
        ReflectionProperty $property
    ) : array {
        $names = array_map(
            static fn(ReflectionAttribute $keyName) : string => $keyName->getArguments()[0],
            $property->getAttributes(KeyName::class)
        );
        if ($names === []) {
            $names = [
                $property->getName()
            ];
        }
        
        $convertCase = $property->getAttributes(self::class)[0] ?? null;
        $caseConverter = $convertCase?->getArguments()[0]
            ?? static fn(string $name) : string => $name;

        return array_map($caseConverter, $names);
    }
}