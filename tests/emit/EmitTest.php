<?php

namespace Endermanbugzjfc\ConfigStruct\emit;

use Endermanbugzjfc\ConfigStruct\KeyName;
use PHPUnit\Framework\TestCase;

class EmitTest extends TestCase
{

    public function testEmitStructPropertySkipping()
    {
        $object = new class() {

            private bool $testPrivateProperty = true;
            protected bool $testProtectedProperty = true;

            public bool $testNotInitialized;

            public $testNoType;
            public bool $testDefaultValue = true;
            public ?bool $testNull = null;

        };
        $output = Emit::emitStruct($object);
        $this->assertTrue(
            $output->getFlattenedValue() === [
                "testNoType" => null,
                "testDefaultValue" => true,
                "testNull" => null
            ]
        );

        $skipped = $output->getSkippedEmptyProperties();
        $this->assertTrue(
            count($skipped) === 1
        );
        $this->assertTrue(
            $skipped
            ["testNotInitialized"]
                ->getName()
            === "testNotInitialized"
        );
    }

    public function testEmitStructCustomKeyName()
    {
        $object = new class() {

            #[KeyName(1)]
            public int $testA = 3;

            #[KeyName(0)]
            public int $testB = 2;

        };
        $output = Emit::emitStruct(
            $object
        );

        $this->assertNotTrue(
            $output->getFlattenedValue() === [
                2,
                3
            ]
        );
        $this->assertTrue(
            $output->getFlattenedValue() === [
                1 => 3,
                0 => 2
            ]
        );
    }

}
