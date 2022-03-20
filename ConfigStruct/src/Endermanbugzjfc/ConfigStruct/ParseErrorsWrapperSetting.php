<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;

class ParseErrorsWrapperSetting
{

    /**
     * @param array $keys A list of error labels that can be used for identifying which error in the tree has just been walked.
     * @param BaseParseError $parseError
     * @return bool False = the error will not be displayed in the final error message.
     */
    public function errorFilter(
        array          $keys,
        BaseParseError $parseError
    ) : bool
    {
        return true;
    }

}