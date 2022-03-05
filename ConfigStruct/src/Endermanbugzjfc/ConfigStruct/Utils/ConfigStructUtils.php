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

}