<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\Group;
use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureError;
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
     * @throws exceptions\StructureError
     * @throws ReflectionException
     */
    public function testStructRecursion()
    {
        $class = TestStructUnsafeRecursiveIndirectB::class;
        $end = TestStructUnsafeRecursiveIndirectC::class;
        $this->expectErrorMessage(
            "Recursion found in struct class $class => ... => $end => loop"
        );

        Analyse::struct(new ReflectionClass(
            new TestStructUnsafeRecursiveIndirectA()
        ), []);
    }

    /**
     * @throws ReflectionException
     * @throws StructureError
     */
    public function testStructConstructor()
    {
        $class = TestStructPrivateConstructor::class;
        $this->expectErrorMessage(
            "Constructor of struct class $class should be public and have 0 arguments"
        );
        Analyse::struct(new ReflectionClass(
            $class
        ), []);
    }

    /**
     * @throws exceptions\StructureError
     */
    public function testPropertyDuplicatedKeyNameArguments()
    {
        $property = (new ReflectionClass(
            new class() {

                #[KeyName("a")] #[KeyName("a")]
                public $testDuplicatedKeyNameArguments;

            }
        ))->getProperties()[0];

        $this->expectError(StructureError::class);
        Analyse::property($property);
    }

    /**
     * @throws exceptions\StructureError
     */
    public function testPropertyGroupWithInvalidType()
    {
        $property = (new ReflectionClass(
            new class() {

                #[Group(0)]
                public int $testGroupWithInvalidType;

            }
        ))->getProperties()[0];

        $this->expectError(StructureError::class);
        Analyse::property($property);
    }

    /**
     * @throws exceptions\StructureError
     * @throws ReflectionException
     */
    public function testPropertyUnionTypesChildStruct()
    {
        $property = (new ReflectionClass(
            new class() {

                public TestStructUnsafeRecursiveIndirectA|string $testGroupWithInvalidType;

            }
        ))->getProperties()[0];

        $this->expectError(StructureError::class);
        Analyse::property($property);
    }

    public function testDoesStructHaveValidConstructor()
    {
        $testNormal = new class() {

            public function __construct()
            {
            }

        };
        $testArgumentDefaultNull = new class() {

            public function __construct(string $a = null)
            {
            }

        };
        $testArgument = new class("") {

            public function __construct(string $a)
            {
            }

        };

        $this->assertTrue(Analyse::doesStructHaveValidConstructor(
            (new ReflectionClass($testNormal))->getConstructor()
        ));
        $this->assertTrue(Analyse::doesStructHaveValidConstructor(
            (new ReflectionClass($testArgumentDefaultNull))->getConstructor()
        ));
        $this->assertNotTrue(Analyse::doesStructHaveValidConstructor(
            (new ReflectionClass($testArgument))->getConstructor()
        ));
        $this->assertNotTrue(Analyse::doesStructHaveValidConstructor(
            (new ReflectionClass(
                TestStructPrivateConstructor::class
            ))->getConstructor()
        ));
    }

    public function testDoesPropertyHaveUnionTypesChildStruct()
    {
        $properties = (new ReflectionClass(
            new class() {

                public $testNoType;

                public TestStructUnsafeRecursiveIndirectA $testOneChildStruct;

                public TestStructUnsafeRecursiveIndirectA|TestStructUnsafeRecursiveIndirectB $testTwoChildStructs;

                public TestStructUnsafeRecursiveIndirectA|string $testOneChildStructAndString;

            }
        ))->getProperties(ReflectionProperty::IS_PUBLIC);

        $this->assertNotTrue(
            Analyse::doesPropertyHaveUnionTypesChildStruct($properties[0])
        );
        $this->assertNotTrue(
            Analyse::doesPropertyHaveUnionTypesChildStruct($properties[1])
        );
        $this->assertTrue(
            Analyse::doesPropertyHaveUnionTypesChildStruct($properties[2])
        );
        $this->assertTrue(
            Analyse::doesPropertyHaveUnionTypesChildStruct($properties[3])
        );
    }

}
