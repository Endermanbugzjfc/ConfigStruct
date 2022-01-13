<?php

namespace Endermanbugzjfc\ConfigStruct;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class AnalyseTest extends TestCase
{

    public function testDoesGroupPropertyHaveInvalidType()
    {
        $a = new class() {
            public $testNoType;
            public mixed $testMixed;
            public array $testArray;
            public bool|int|float|string|array $testUnion;
        };
        foreach (
            (new ReflectionClass($a))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            $this->assertNotTrue(
                Analyse::doesGroupPropertyHaveInvalidType($property)
            );
        }

        $b = new class() {
            public string $testString;
            public bool|int|float|string $testUnionIncompatible;
        };
        foreach (
            (new ReflectionClass($b))
                ->getProperties(ReflectionProperty::IS_PUBLIC)
            as $property
        ) {
            $this->assertTrue(
                Analyse::doesGroupPropertyHaveInvalidType($property)
            );
        }

    }

    public function testRecursion()
    {

    }

    public function testDoesKeyNameHaveDuplicatedArgument()
    {

    }

    public function testStruct()
    {

    }

    public function testProperty()
    {

    }
}
