<?php /** @noinspection PhpUnusedPrivateFieldInspection */

/** @noinspection PhpMissingFieldTypeInspection */

namespace Endermanbugzjfc\ConfigStruct;

use PHPUnit\Framework\TestCase;

class EmitTest extends TestCase
{

    public function testObjectPropertySkipping()
    {
        $object = new class() {

            private bool $testPrivateProperty = true;
            protected bool $testProtectedProperty = true;

            public bool $testNotInitialized;

            public $testNoType;
            public bool $testDefaultValue = true;
            public ?bool $testNull = null;

        };
        $output = Emit::object($object);
        $this->assertTrue(
            $output === [
                "testNoType" => null,
                "testDefaultValue" => true,
                "testNull" => null
            ]
        );
    }

    public function testObjectCustomKeyName()
    {
        $object = new class() {

            #[KeyName(1)]
            public int $testA = 3;

            #[KeyName(0)]
            public int $testB = 2;

        };
        $output = Emit::object(
            $object
        );

        $this->assertNotTrue(
            $output === [
                2,
                3
            ]
        );
        $this->assertTrue(
            $output === [
                1 => 3,
                0 => 2
            ]
        );
    }


    public function testObjectRecursiveChildren()
    {
        $root = new class() {

            public string $testA;

            public self $testSelf;

        };
        $oneDeep = clone $root;
        $twoDeep = clone $root;

        $root->testA = "testA";
        $root->testSelf = $oneDeep;
        $oneDeep->testA = "testB";
        $oneDeep->testSelf = $twoDeep;

        $output = Emit::object(
            $root
        );

        $this->assertTrue(
            $output === [
                "testA" => "testA",
                "testSelf" => [
                    "testA" => "testB",
                    "testSelf" => [

                    ]
                ]
            ]
        );
    }

}
