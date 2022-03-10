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
        string $message,
        object $object,
        ?string $property
    ) : void
    {
        try {
            $context = Parse::object(
                [],
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

    public function test__constructDuplicatedKeyNames()
    {

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
