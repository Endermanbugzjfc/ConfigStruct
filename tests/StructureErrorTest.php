<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use PHPUnit\Framework\TestCase;

class StructureErrorTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    private function expectPreviousExceptionMessage(
        string  $message,
        object  $object,
        ?string $property
    ) : void
    {
        try {
            $context = Parse::object(
                [
                    "testThreeDuplicatedListTypes" => [
                    ],
                    "testSelf" => [
                    ]
                ],
                $object
            );
            $context->copyToObject(
                $object,
                "root object"
            );
        } catch (StructureError $err) {
            $class = $object::class;
            $expected = "Invalid structure in $class";
            if ($property !== null) {
                $expected .= "->$property";
            }

            $this->assertTrue(
                $err->getMessage() === $expected
            );
            echo $err->getPrevious()->getMessage() . "\n";
            $this->assertTrue(
                $err->getPrevious()->getMessage() === $message
            );
            return;
        }
        throw new AssertionError(
            "No " . StructureError::class . " had been thrown"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function test__constructDuplicatedKeyNames()
    {
        $object = new class() {

            #[KeyName(-1)] #[KeyName("-1")]
            #[KeyName(0)] #[KeyName("0")]
            #[KeyName("kjaldf")] #[KeyName("kjaldf")]
            public bool $testThreeDuplicatedKeyNames;

        };
        $this->expectPreviousExceptionMessage(
            'Duplicated key names "-1", "0", "kjaldf"',
            $object,
            null
        );
    }

    public function test__constructDuplicatedListTypes()
    {

    }

    public function test__constructInvalidListTypes()
    {

    }

    public function test__constructObjectConstructorProtected()
    {

    }

    public function test__constructObjectConstructorWithArguments()
    {

    }

}
