<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use Endermanbugzjfc\ConfigStruct\ParseError\BaseParseError;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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
            [
                "testIndentationWithList" => [
                    "a" => null
                ],
                "testErrorFilter" => null
            ],
            $object
        );
        try {
            $context->copyToObject(
                $object,
                "root object"
            );
        } catch (ParseErrorsWrapper $err) {
            return $err;
        }
        throw new RuntimeException(
            "No errors when copy parsed data to object"
        );
    }

    public function testGetErrorFilter()
    {
        $err = self::parseErrorsWrapperProvider();
        $err->regenerateErrorMessage(
            $err->getRootHeaderLabel(),
            $err->getIndentation(),
            fn(
                array $keys,
                BaseParseError $parseError
            ) : bool => $keys !== [
                    "root object",
                    "element \"testErrorFilter\""
                ]
        );
        echo $err->getMessage();
    }

    public function testRegenerateErrorMessage()
    {

    }

    public function testGetIndentation()
    {

    }
}
