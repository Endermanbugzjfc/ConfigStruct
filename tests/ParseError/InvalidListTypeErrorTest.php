<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use Endermanbugzjfc\ConfigStruct\ListType;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\ConfigStruct\ParseError;
use PHPUnit\Framework\TestCase;

class InvalidListTypeErrorTest extends TestCase
{

    /**
     * @throws ParseError
     */
    public function testGetMessage()
    {
        $object = new class() {

            #[ListType("akdfj")]
            public array $testOneInvalidListType;

            #[ListType("akdfj")] #[ListType("nklvnj")]
            public array $testMultipleInvalidListType;

            #[ListType(self::class)] #[ListType("akdfj")] #[ListType("nklvnj")]
            public array $testMixedListType;

        };
        $context = Parse::object(
            [
                "testOneInvalidListType" => [
                ],
                "testMultipleInvalidListType" => [
                ],
                "testMixedListType" => [
                ],
            ],
            $object
        );
        $this->expectExceptionMessage(
            <<<EOT
            5 errors in object root
                1 errors in element "testOneInvalidListType"
                    Invalid list type "akdfj"
                2 errors in element "testMultipleInvalidListType"
                    Invalid list type "akdfj"
                    Invalid list type "nklvnj"
                2 errors in element "testMixedListType"
                    Invalid list type "akdfj"
                    Invalid list type "nklvnj"
            EOT
        );
        $context->copyToObject(
            $object,
            "object root"
        );
    }
}
