<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use PHPUnit\Framework\TestCase;

class ParseTest extends TestCase
{

    public function testParseStructPropertyAccessLevels()
    {
        $object = new class() {

            private $testPrivateProperty;

            protected $testProtectedProperty;

            public $testPublicProperty;

        };
        $clone = clone $object;
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

        $clone->testPublicProperty = null;
        $this->assertTrue(
            $object == $clone
        );
    }
}
