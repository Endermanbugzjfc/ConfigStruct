<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
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

    /**
     * @throws ReflectionException
     */
    public function testRecursion()
    {
        $a = (new class() {
        })::class;
        $b = (new class() {
        })::class;
        $c = (new class() {
        })::class;
        $testRecursive = (new #[Recursive] class() {
        })::class;

        $aTrace = [$a];
        $this->assertTrue(Analyse::recursion(
                $a,
                $aTrace
            ) === $a);
        $this->assertTrue($aTrace === [$a, $a]);

        $bTrace = [$a, $b, $c];
        $this->assertTrue(Analyse::recursion(
                $b,
                $bTrace
            ) === $c);
        $this->assertTrue($bTrace === [$a, $b, $c, $b]);

        $testRecursiveTrace = [];
        $this->assertTrue(Analyse::recursion(
                $testRecursive,
                $testRecursiveTrace
            ) === null);
        $this->assertTrue($testRecursiveTrace === [$testRecursive]);
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
