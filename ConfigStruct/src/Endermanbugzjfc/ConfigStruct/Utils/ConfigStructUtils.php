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

    public static function arrayDiffRecursive(
        array $a,
        array $b
    ) : array
    {
        // Credit: https://stackoverflow.com/questions/3876435/recursive-array-diff
        $return = [];

        foreach ($a as $k => $v) {
            if (array_key_exists($k, $b)) {
                if (is_array($v)) {
                    $recursiveDiff = self::arrayDiffRecursive(
                        $v,
                        $b[$k]
                    );
                    if (!empty(
                    $recursiveDiff
                    )) {
                        $return[$k] = $recursiveDiff;
                    }
                } else {
                    if ($v != $b[$k]) {
                        $return[$k] = $v;
                    }
                }
            } else {
                $return[$k] = $v;
            }
        }
        return $return;
    }

}