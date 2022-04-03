<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;
use PHPUnit\Framework\TestCase;

class ParseErrorsWrapperTest extends TestCase
{
    private static function parseErrorsWrapperProvider() : ParseErrorsWrapper
    {
        $object = new class() {
            #[ListType(A::class)]
            public array $testIndentationWithList;

            public bool $testErrorFilter;
        };
        $context = Parse::object(
            $object,
            [
                "testIndentationWithList" => [
                    "a" => null
                ],
                "testErrorFilter" => null
            ]
        );
        try {
            $context->copyToObject(
                $object,
                "root object"
            );
        } catch (ParseErrorsWrapper $err) {
            return $err;
        }
        throw new AssertionError(
            "No errors when copy parsed data to object"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testRegenerateErrorMessageFilterSpecifiedError()
    {
        $err = self::parseErrorsWrapperProvider();
        $err->regenerateErrorMessage(
            $err->getRootHeaderLabel(),
            $err->getIndentation(),
            fn(
                array          $keys,
                BaseParseError $parseError
            ) : bool => $keys !== [
                "root object",
                "element \"testErrorFilter\""
            ]
        );

        $this->expectExceptionMessage(
            <<<EOT
            1 errors in root object
                1 errors in element "testIndentationWithList"
                    Element is null while it should be array
            
            EOT
        );
        throw $err;
    }

    public function testRegenerateErrorMessageFilterAllError()
    {
        $err = self::parseErrorsWrapperProvider();
        $err->regenerateErrorMessage(
            $err->getRootHeaderLabel(),
            $err->getIndentation(),
            $filter = fn(
                array          $keys,
                BaseParseError $parseError
            ) : bool => false
        );

        $this->assertTrue(
            $err->getMessageRtrim() === ""
        );
        $this->assertTrue(
            $err->getErrorFilter() === $filter
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testRegenerateErrorMessageIndentation()
    {
        $err = self::parseErrorsWrapperProvider();
        $indentation = "----";
        $err->regenerateErrorMessage(
            $err->getRootHeaderLabel(),
            $indentation
        );

        $this->assertTrue(
            $err->getIndentation() === $indentation
        );
        $this->expectExceptionMessage(
            <<<EOT
            2 errors in root object
            ----1 errors in element "testIndentationWithList"
            --------Element is null while it should be array
            ----1 errors in element "testErrorFilter"
            --------Element is null while it should be bool
            
            EOT
        );
        throw $err;
    }
}
