<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use function implode;

/**
 * When an element has one or more corresponding properties but its key name has the wrong text case.
 */
final class KeyNameTextCaseMismatchError extends BaseParseError
{

    /**
     * @param string[] $guessNames
     * @param string $inputName
     */
    public function __construct(
        protected array  $guessNames,
        protected string $inputName
    ) {
        parent::__construct(null);
    }

    /**
     * @return string[]
     */
    public function getGuessNames() : array
    {
        return $this->guessNames;
    }


    public function getInputName() : string
    {
        return $this->inputName;
    }

    public function getMessage() : string
    {
        $guessNames = implode(
            "\" / \"",
            $this->getGuessNames()
        );
        return "Element key name \"{$this->getInputName()}\" has the wrong text case, guess it is supposed to \"$guessNames\"";
    }
}