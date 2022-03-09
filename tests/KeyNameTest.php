<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use PHPUnit\Framework\TestCase;

class KeyNameTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    public function test__construct()
    {
        // TODO: Test for emit.
        $object = new class () {

            #[KeyName("a")]
            public mixed $testOneKeyName;

            #[KeyName("b")] #[KeyName("c")]
            public mixed $testMultipleKeyNames;

        };
        $context = Parse::object(
            [
                "a" => "a",
                "b" => "b",
                "c" => "c"
            ],
            $object
        );
        $context->copyToObject(
            $object,
            "root object"
        );

        $this->assertTrue(
            $object->testOneKeyName === "a"
        );
        $this->assertTrue(
            $object->testMultipleKeyNames === "b"
        );

        $context = Parse::object(
            [
                "c" => "c"
            ],
            $object
        );
        $context->copyToObject(
            $object,
            "root object"
        );

        $this->assertTrue(
            $object->testMultipleKeyNames === "c"
        );
    }
}
