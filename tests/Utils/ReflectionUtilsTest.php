<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use Endermanbugzjfc\ConfigStruct\Dummy\Extending\B;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use function array_diff;

class ReflectionUtilsTest extends TestCase
{
    /**
     * @param mixed[] $a
     * @param mixed[] $b
     */
    private function assertEqualsArrayDiff(
        array $a,
        array $b
    ) : void {
        $this->assertTrue(
            array_diff(
                $a,
                $b
            ) === [
            ]
        );
    }

    public function testGetPropertyTypes()
    {
        $object = new class() {
            public bool $testOneType;
            public ?bool $testNullable;
            public A $testOneClass;

            public bool|int|float|string $testScalars;
            public A|B $testTwoClasses;
            public self|array|null $testSelfAndArrayNullable;
        };
        $reflect = new ReflectionClass(
            $object
        );
        $properties = $reflect->getProperties(
            ReflectionProperty::IS_PUBLIC
        );
        $results = [];
        foreach ($properties as $index => $property) {
            $raws = [];
            $types = ReflectionUtils::getPropertyTypes(
                $property
            );
            foreach ($types as $type) {
                $raws[] = $type->getName();
            }

            switch ($index) {
                case 0:
                case 1: // Does not detect nullability here.
                    $this->assertEqualsArrayDiff(
                        $raws,
                        [
                            "bool"
                        ]
                    );
                    break;

                case 2:
                    $this->assertEqualsArrayDiff(
                        $raws,
                        [
                            A::class
                        ]
                    );
                    break;

                case 3:
                    $this->assertEqualsArrayDiff(
                        $raws,
                        [
                            "bool",
                            "int",
                            "float",
                            "string"
                        ]
                    );
                    break;

                case 4:
                    $this->assertEqualsArrayDiff(
                        $raws,
                        [
                            A::class,
                            B::class
                        ]
                    );
                    break;

                case 5:
                    $this->assertEqualsArrayDiff(
                        $raws,
                        [
                            "self",
                            "array",
                            "null"
                        ]
                    );
                    break;

                default:
                    $propertyName = $property->getName();
                    throw new RuntimeException(
                        "No test for property \"$propertyName\""
                    );

            }
        }

        // TODO: Test for intersection type (?).
    }
}