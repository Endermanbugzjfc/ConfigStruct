<?php /** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpMissingFieldTypeInspection */

/** @noinspection PhpUnused */

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\attributes\Recursive;
use Endermanbugzjfc\ConfigStruct\attributes\TypedArray;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureError;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use const E_RECOVERABLE_ERROR;

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

    public function testStructCatchingStructureError()
    {
        $class = TestStructPrivateConstructor::class;
        try {
            Analyse::struct(new ReflectionClass(
                $class
            ), []);
        } catch (StructureError $error) {
            $this->assertTrue(
                $error->getCode() === E_RECOVERABLE_ERROR
            );
        }
    }

    public function testPropertyDuplicatedKeyNameArguments()
    {
        $property = (new ReflectionClass(
            new class() {

                #[KeyName("a")] #[KeyName("a")]
                public $testDuplicatedKeyNameArguments;

            }
        ))->getProperties()[0];

        $this->expectErrorMessage(
            "Property {$property->getDeclaringClass()}->{$property->getName()} used two key names which is exactly the same"
        );
        Analyse::property($property);
    }

    public function testPropertyGroupWithInvalidType()
    {
        $property = (new ReflectionClass(
            new class() {

                #[TypedArray(0)]
                public int $testGroupWithInvalidType;

            }
        ))->getProperties()[0];

        $this->expectErrorMessage("Property {$property->getDeclaringClass()}->{$property->getName()} is a group but its type is not compatible");
        Analyse::property($property);
    }

    public function testPropertyUnionTypesChildStruct()
    {
        $property = (new ReflectionClass(
            new class() {

                public TestStructUnsafeRecursiveIndirectA|string $testGroupWithInvalidType;

            }
        ))->getProperties()[0];

        $this->expectErrorMessage("Property {$property->getDeclaringClass()}->{$property->getName()} used union-types child struct which is not supported in this ConfigStruct version");
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
