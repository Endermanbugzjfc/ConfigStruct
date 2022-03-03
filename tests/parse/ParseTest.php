<?php /** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpUnused */

/** @noinspection PhpUnusedPrivateFieldInspection */

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use Endermanbugzjfc\ConfigStruct\Parse;
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

        $this->assertTrue(
            $output->getPropertiesOutput()
            ["testPublicProperty"]
                ->getKeyName() === "testPublicProperty"
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

    public function testParseStructKeyNameCandidatesAndUnhandledElements()
    {
        $testIndexKeyNameCandidate = new class() {

            #[KeyName(0)] #[KeyName("")]
            public $testA;

        };
        $testEmptyStringKeyNameCandidate = clone $testIndexKeyNameCandidate;

        $testUnhandledElements = Parse::parseStruct(
            $testIndexKeyNameCandidate,
            [
                "testA" => "testA",
                null => "",
                0
            ]
        );
        $this->assertTrue(
            $testIndexKeyNameCandidate->testA === 0
        );
        $this->assertTrue(
            $testUnhandledElements->getUnhandledElements() === [
                "testA" => "testA",
                null => ""
            ]
        );

        Parse::parseStruct(
            $testEmptyStringKeyNameCandidate,
            [
                null => ""
            ]
        );
        $this->assertTrue(
            $testEmptyStringKeyNameCandidate->testA === ""
        );
    }

    public function testParseStructMissingElements()
    {
        $object = new class() {

            public $testNoDefaultValue;

            public $testDefaultValue = true;

        };
        $output = Parse::parseStruct(
            $object,
            [

            ]
        );

        $this->assertTrue(
            $output->getMissingElements()
            ["testNoDefaultValue"]
                ->getName() === "testNoDefaultValue"
        );
        $this->assertTrue(
            $output->getMissingElements()
            ["testDefaultValue"]
                ->getName() === "testDefaultValue"
        );
    }

    public function testParseStructChildStructRecursive()
    {
        $object = new class() {

            public $testA;

            public self $testSelf;

        };
        $class = $object::class;
        Parse::parseStruct(
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
