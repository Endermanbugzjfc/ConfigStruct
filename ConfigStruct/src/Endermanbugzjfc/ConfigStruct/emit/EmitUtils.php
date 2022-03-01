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
     * Filter non-public properties from an array that is converted from object.
     * @param array $array
     * @return void
     */
    public static function filterNonPublic(
        array &$array
    ) : void {
        foreach ($array as $key => $value) {
            if (str_contains(
                $key,
                "\000"
            )) {
                unset(
                    $array[$key]
                );
            }
        }
    }

}