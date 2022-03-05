<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Closure;
use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;
use Exception;
use function array_unshift;
use function implode;
use function str_repeat;
use const E_RECOVERABLE_ERROR;

final class ParseError extends Exception
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
     * @return Closure|null Should have 0 arguments and return bool. False = the error will not be displayed in the final error message.
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
     * @param Closure|null $errorFilter
     * @return void
     * @see ParseError::errorsTreeToString()
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
        );
    }

    /**
     * @param array $tree The errors tree.
     * @param string $label The label that will be displayed in the first line (header). File path should be given if the parsed data was from a file.
     * @param string $indentation Indentation per depth, to make the errors tree more readable for human. Four spaces by default.
     * @param Closure|null $errorFilter See {@link ParseError::getErrorFilter()}.
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
            $label,
            $indentation,
            0,
            $errorFilter
        );
    }

    protected static function errorsTreeToStringRecursive(
        array    $tree,
        string   $label,
        string   $defaultIndentation,
        int      $depth,
        ?Closure $errorFilter,
        ?int     &$count = null
    ) : string
    {
        $lines = [];
        $indentation = str_repeat(
            $defaultIndentation,
            $depth + 1
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
                !$errorFilter()
            ) {
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
                $errorFilter,
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
