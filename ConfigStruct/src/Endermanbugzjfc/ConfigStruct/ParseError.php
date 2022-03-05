<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Exception;
use function array_unshift;
use function implode;
use function is_string;
use function str_repeat;

final class ParseError extends Exception
{

    /**
     * @param array $tree The errors tree.
     * @param string $label The label that will be displayed in the first line (header). File path should be given if the parsed data was from a file.
     * @param string $indentation Indentation per depth, to make the errors tree more readable for human. Four spaces by default.
     * @return string
     */
    public static function errorsTreeToString(
        array  $tree,
        string $label,
        string $indentation = "    "
    ) : string
    {
        return self::errorsTreeToStringRecursive(
            $tree,
            $label,
            $indentation,
            0
        );
    }

    protected static function errorsTreeToStringRecursive(
        array  $tree,
        string $label,
        string $defaultIndentation,
        int    $depth,
        ?int   &$count = null
    ) : string
    {
        $lines = [];
        $indentation = str_repeat(
            $defaultIndentation,
            $depth + 1
        );

        $count = 0;
        foreach ($tree as $key => $content) {
            if (!is_string(
                $content
            )) {
                $children[$key] = $content;
                continue;
            }
            $count++;
            $lines[] = $indentation . $content;
        }
        unset($tree);
        foreach ($children ?? [] as $key => $child) {
            self::errorsTreeToStringRecursive(
                $child,
                $key,
                $defaultIndentation,
                $depth + 1,
                $count
            );
        }

        $indentation = str_repeat(
            $defaultIndentation,
            $depth
        );
        array_unshift(
            $lines,
            $indentation . "$count errors in $label"
        );
        return implode(
            "\n",
            $lines
        );
    }

}