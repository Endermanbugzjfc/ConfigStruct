<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ParseErrorsWrapperTest extends TestCase
{

    private static function parseErrorsWrapperProvider() : ParseErrorsWrapper
    {
        $object = new class() {

            #[ListType(A::class)]
            public $testIndentationWithList;

        };
        $context = Parse::object(
            [
                "testIndentationWithList" => [
                    "a" => null
                ]
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

    }

    public function testRegenerateErrorMessage()
    {

    }

    public function testGetIndentation()
    {

    }
}
