<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{

    public function testParseStructPropertyAccessLevels()
    {
        $object = new class() {

            private ?bool $testPrivateProperty;

            protected ?bool $testProtectedProperty;

            public ?bool $testPublicProperty;

        };
        $output = Parse::parseStruct(
            $object,
            [
                "testPrivateProperty" => null,
                "testProtectedProperty" => null,
                "testPublicProperty" => null
            ]
        );

        $this->assertTrue(
            count($output->getPropertiesOutput()) === 1
        );

        $property = $output->getPropertiesOutput()[0];
        $this->assertTrue(
            $property->getKeyName() === "testPublicProperty"
        );

        [
            $private,
            $protected,
            $public
        ] = $output->getReflection()->getProperties();
        $private->setAccessible(true);
        $protected->setAccessible(true);
        $this->assertNotTrue($private->isInitialized($object));
        $this->assertNotTrue($protected->isInitialized($object));
        $this->assertTrue($public->isInitialized($object));
        $this->assertTrue($public->getValue($object) === null);
    }
}
