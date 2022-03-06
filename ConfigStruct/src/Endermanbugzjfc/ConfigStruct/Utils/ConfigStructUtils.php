<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use function array_values;

final class ConfigStructUtils
{

    /**
     * A probably worse implementation of {@link \array_is_list()}, for PHP 8.0. Consider PR if you want to improve.
     * @param array $array
     * @return bool
     */
    public static function arrayIsList(
        array $array
    ) : bool
    {
        return array_values(
                $array
            ) === $array;
    }

    /**
     * @param array $array
     * @param callable $callback See test file for full signatures.
     * @param array $keys
     * @return void
     */
    public static function arrayUnsetRecursive(
        array    &$array,
        callable $callback,
        array    $keys = []
    ) : void
    {
        // Credit: https://stackoverflow.com/questions/9150726/unset-inside-array-walk-recursive-not-working
        foreach ($array as $key => &$value) {
            $keysClone = $keys;
            $keysClone[] = $key;
            if ($callback(
                $keysClone,
                $value
            )) {
                unset(
                    $array[$key]
                );
                continue;
            }

            if (is_array($value)) {
                self::arrayUnsetRecursive(
                    $value,
                    $callback,
                    $keysClone
                );
            }
        }
    }

}