<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
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
        $a = new ReflectionClass(
            TestStructUnsafeRecursiveIndirectA::class
        );
        $b = new ReflectionClass(
            TestStructUnsafeRecursiveIndirectB::class
        );
        $c = new ReflectionClass(
            TestStructUnsafeRecursiveIndirectC::class
        );

        $testRecursive = new ReflectionClass(new #[Recursive] class() {
        });

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

        $testNoRecursion = new ReflectionClass(new class() {
        });
        $testNoRecursionTrace = [];
        $this->assertTrue(Analyse::recursion(
                $testNoRecursion,
                $testNoRecursionTrace
            ) === null);
        $this->assertTrue($testNoRecursionTrace === [$testNoRecursion]);
    }

    public function testDoesKeyNameHaveDuplicatedArgument()
    {
        $properties = (new ReflectionClass(
            new class() {

                #[KeyName("a")] #[KeyName("b")]
                public $testKeyNameNoDuplicatedArguments;

                #[KeyName("a")] #[KeyName("a")]
                public $testKeyNameOneNameTwoDuplicatedArguments;

            }
        ))->getProperties(ReflectionProperty::IS_PUBLIC);

        $this->assertNotTrue(Analyse::doesKeyNameHaveDuplicatedArgument(
            ...$properties[0]->getAttributes(KeyName::class)
        ));
        $this->assertTrue(Analyse::doesKeyNameHaveDuplicatedArgument(
            ...$properties[1]->getAttributes(KeyName::class)
        ));
    }

    /**
     * @throws exceptions\StructureException
     * @throws ReflectionException
     */
    public function testStructRecursion()
    {
        $class = TestStructUnsafeRecursiveIndirectB::class;
        $end = TestStructUnsafeRecursiveIndirectC::class;
        $this->expectExceptionMessage(
            "Recursion found in struct class $class => ... => $end => loop"
        );

        Analyse::struct(new ReflectionClass(
            new TestStructUnsafeRecursiveIndirectA()
        ), []);
    }

    public function testStructConstructor()
    {

    }

    /**
     * @throws exceptions\StructureException
     */
    public function testPropertyDuplicatedKeyNameArguments()
    {
        $property = (new ReflectionClass(
            new class() {

                #[KeyName("a")] #[KeyName("a")]
                public $testDuplicatedKeyNameArguments;

            }
        ))->getProperties()[0];

        $this->expectException(StructureException::class);
        Analyse::property($property);
    }

    /**
     * @throws exceptions\StructureException
     */
    public function testPropertyGroupWithInvalidType()
    {
        $property = (new ReflectionClass(
            new class() {

                #[Group(0)]
                public int $testGroupWithInvalidType;

            }
        ))->getProperties()[0];

        $this->expectException(StructureException::class);
        Analyse::property($property);
    }

    public function testDoesStructHasValidConstructor()
    {

    }

}
