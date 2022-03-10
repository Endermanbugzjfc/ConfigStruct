<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\Dummy\StructureError\ConstructorProtected;
use Endermanbugzjfc\ConfigStruct\Dummy\StructureError\DuplicatedStructCandidates;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use function class_exists;

class StructureErrorTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    private function expectPreviousExceptionMessage(
        string $message,
        object $object,
        ?string $property,
        array $input
    ) : void
    {
        try {
            $context = Parse::object(
                $input,
                $object
            );
            $context->copyToNewObject(
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
            null,
            [
            ]
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function test__constructDuplicatedListTypes()
    {
        $object = new DuplicatedStructCandidates();
        $keyName = "testThreeDuplicatedListTypes";
        $this->expectPreviousExceptionMessage(
            "Duplicated struct candidates Endermanbugzjfc\ConfigStruct\Dummy\StructureError\DuplicatedStructCandidates, Endermanbugzjfc\ConfigStruct\Dummy\Extending\A, Endermanbugzjfc\ConfigStruct\Dummy\Extending\B",
            $object,
            $keyName,
            [
                $keyName => [
                    [
                        null
                    ]
                ]
            ]
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function test__constructInvalidListTypes()
    {
        $object = new class() {

            #[ListType("ajbfl")]
            public string $testThreeInvalidListTypes;

        };
        $reflection = new ReflectionClass(
            $object
        );
        $keyName = "testThreeInvalidListTypes";
        try {
            $property = $reflection->getProperty(
                $keyName
            );
        } catch (ReflectionException $err) {
            throw new AssertionError(
                "Property does not exist",
                0,
                $err
            );
        }
        $listType = $property->getAttributes(
            ListType::class
        )[0];
        $class = $listType->getArguments()[0];
        $this->assertNotTrue(
            class_exists(
                $class
            )
        );

        $this->expectPreviousExceptionMessage(
            "List type attribute has invalid class",
            $object,
            $keyName,
            [
                $keyName => [
                ]
            ]
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    private function failedToCreateANewObjectFromReflection(
        object $object
    ) : void {
        $this->expectPreviousExceptionMessage(
            "Failed to create a new object from reflection",
            $object,
            null,
            [
            ]
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function test__constructObjectConstructorProtected()
    {
        $object = ConstructorProtected::create();
        $this->failedToCreateANewObjectFromReflection(
            $object
        );
    }

    public function test__constructObjectConstructorWithArguments()
    {

    }

}
