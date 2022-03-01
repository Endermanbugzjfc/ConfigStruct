<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\emit;

use function str_contains;

final class EmitUtils
{

    /**
     * This class should be used statically!
     */
    private function __construct()
    {
    }

    /**
     * Filter non-public property names (contains character "\000") from an array of property names.
     * @param string[] $array
     * @return void
     */
    public static function filterNonPublicNames(
        array &$array
    ) : void
    {
        foreach ($array as $name) {
            if (str_contains(
                $name,
                "\000"
            )) {
                unset(
                    $array[$name]
                );
            }
        }
    }

}