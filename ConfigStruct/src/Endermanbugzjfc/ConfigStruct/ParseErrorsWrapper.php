<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Closure;
use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;
use Exception;
use function array_unshift;
use function count;
use function implode;
use function rtrim;
use function str_repeat;
use const E_RECOVERABLE_ERROR;

final class ParseErrorsWrapper extends Exception
{

    protected ?Closure $errorFilter = null;

    protected string $indentation = "    ";

    public function __construct(
        protected array  $errorsTree,
        protected string $rootHeaderLabel
    )
    {
        $message = $this->generateErrorMessage();
        parent::__construct(
            $message,
            E_RECOVERABLE_ERROR
        );
    }

    /**
     * @return array
     */
    public function getErrorsTree() : array
    {
        return $this->errorsTree;
    }

    /**
     * @return string The label that will be displayed in the first line (header). File path should be given if the parsed data was from a file.
     */
    public function getRootHeaderLabel() : string
    {
        return $this->rootHeaderLabel;
    }

    /**
     * @return Closure|null Should have exactly 2 arguments and return bool. False = the error will not be displayed in the final error message. The first argument is array $keys, a list of error labels that can be used for identifying which error in the tree has just been walked. The second argument is {@link BaseParseError} $parseError, the error itself.
     */
    public function getErrorFilter() : ?Closure
    {
        return $this->errorFilter;
    }

    /**
     * @return string
     */
    public function getIndentation() : string
    {
        return $this->indentation;
    }

    /**
     * @param string $rootHeaderLabel
     * @param string $indentation
     * @param Closure|null $errorFilter See {@link ParseErrorsWrapper::getErrorFilter()}.
     * @return void
     * @see ParseErrorsWrapper::errorsTreeToString()
     */
    public function regenerateErrorMessage(
        string  $rootHeaderLabel,
        string  $indentation = "    ",
        Closure $errorFilter = null
    ) : void
    {
        $this->rootHeaderLabel = $rootHeaderLabel;
        $this->errorFilter = $errorFilter;
        $this->indentation = $indentation;

        $this->message = $this->generateErrorMessage();
    }

    protected function generateErrorMessage() : string
    {
        $tree = $this->getErrorsTree();
        $label = $this->getRootHeaderLabel();
        return self::errorsTreeToString(
                $tree,
                $label,
                $this->getIndentation(),
                $this->getErrorFilter()
            ) . "\n";
    }

    /**
     * @param array $tree The errors tree.
     * @param string $label The label that will be displayed in the first line (header). File path should be given if the parsed data was from a file.
     * @param string $indentation Indentation per depth, to make the errors tree more readable for human. Four spaces by default.
     * @param Closure|null $errorFilter See {@link ParseErrorsWrapper::getErrorFilter()}.
     * @return string
     */
    public static function errorsTreeToString(
        array    $tree,
        string   $label,
        string   $indentation = "    ",
        ?Closure $errorFilter = null
    ) : string
    {
        return self::errorsTreeToStringRecursive(
            $tree,
            [
                $label
            ],
            $indentation,
            $errorFilter
        )[0] ?? "";
    }

    protected static function errorsTreeToStringRecursive(
        array    $tree,
        array    $keys,
        string   $defaultIndentation,
        ?Closure $errorFilter
    ) : array
    {
        $lines = [];
        $depth = count($keys);
        $indentation = str_repeat(
            $defaultIndentation,
            $depth
        );

        $count = 0;
        foreach ($tree as $key => $content) {
            if (!$content instanceof BaseParseError) {
                $children[$key] = $content;
                continue;
            }

            if (
                $errorFilter !== null
                and
                !$errorFilter(
                    $keys,
                    $content
                )
            ) {
                continue;
            }
            $count++;
            $lines[] = $indentation . $content;
        }
        unset($tree);
        foreach ($children ?? [] as $key => $child) {
            $keysClone = $keys;
            $keysClone[] = $key;
            [
                $newLines,
                $newCount
            ] = self::errorsTreeToStringRecursive(
                $child,
                $keysClone,
                $defaultIndentation,
                $errorFilter
            );
            $count += $newCount;
            if ($newLines !== null) {
                $lines[] = $newLines;
            }
        }
        if ($lines !== []) {
            $indentation = str_repeat(
                $defaultIndentation,
                $depth - 1
            );
            $label = $keys[$depth - 1];
            array_unshift(
                $lines,
                $indentation . "$count errors in $label"
            );
            return [
                implode(
                    "\n",
                    $lines
                ),
                $count
            ];
        }
        return [
            null,
            0
        ];
    }

    /**
     * Trim out the trailing line break and other whitespaces.
     * @return string
     */
    public function getMessageRtrim() : string
    {
        return rtrim(
            $this->getMessage()
        );
    }

}
