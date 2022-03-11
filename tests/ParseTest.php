<?php

namespace Endermanbugzjfc\ConfigStruct;

use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectPropertyAccessLevels()
    {
        $object = new class() {

            private ?bool $testPrivateProperty;

            protected ?bool $testProtectedProperty;

            public ?bool $testPublicProperty;
        };

        $keyName = "testPublicProperty";
        $context = Parse::object(
            $object,
            [
                "testPrivateProperty" => null,
                "testProtectedProperty" => null,
                $keyName => null
            ]
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        $this->assertTrue(
            $context->getPropertyContexts()[$keyName]->getDetails()->getKeyName()
            === $keyName
        );

        [
            $private,
            $protected,
            $public
        ] = $context->getReflection()->getProperties();
        $private->setAccessible(true);
        $protected->setAccessible(true);
        $this->assertNotTrue($private->isInitialized($object));
        $this->assertNotTrue($protected->isInitialized($object));
        $this->assertTrue($public->isInitialized($object));
        $this->assertTrue($public->getValue($object) === null);
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectUnhandledElements()
    {
        $object = new class() {

        };

        $context = Parse::object(
            $object,
            [
                "testA" => "testA",
                null => "",
                0
            ]
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        $this->assertTrue(
            $context->getUnhandledElements() === [
                "testA" => "testA",
                null => "",
                0
            ]
        );
    }

    public function testObjectMissingElements()
    {
        $object = new class() {

            public bool $testNoDefaultValue;

            public bool $testDefaultValue = true;

        };
        $context = Parse::object(
            $object,
            [

            ]
        );

        $this->assertTrue(
            $context->getMissingElements()
            ["testNoDefaultValue"]
                ->getName() === "testNoDefaultValue"
        );
        $this->assertTrue(
            $context->getMissingElements()
            ["testDefaultValue"]
                ->getName() === "testDefaultValue"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectChildStructRecursive()
    {
        $object = new class() {

            public string $testA;

            public self $testSelf;

        };
        $class = $object::class;
        $context = Parse::object(
            $object,
            [
                "testA" => "testA",
                "testSelf" => [
                    "testA" => "testB",
                    "testSelf" => [
                    ]
                ]
            ]
        );
        $context->copyToObject(
            $object,
            "root object"
        );

        $this->assertTrue(
            $object->testA === "testA"
        );

        $oneDeeper = $object->testSelf;
        $this->assertTrue(
            $oneDeeper instanceof $class
        );
        $this->assertTrue(
            $oneDeeper->testA === "testB"
        );

        $twoDeeper = $oneDeeper->testSelf;
        $this->assertTrue(
            $twoDeeper instanceof $class
        );
        $this->assertTrue(
            !isset($twoDeeper->testA)
        );
        $this->assertTrue(
            !isset($twoDeeper->testSelf)
        );
    }

}
