<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\ConfigStruct\ParseContext\ObjectContext;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use PHPUnit\Framework\TestCase;
use const PHP_FLOAT_MAX;

class TypeMismatchErrorTest extends TestCase
{

    /**
     * @noinspection PhpMissingReturnTypeInspection No auto completion for properties. >:(
     */
    private static function objectProvider()
    {
        return new class () {

            public bool $testBool;
            public int $testInt;
            public float $testFloat;
            public string $testString;

        };
    }

    /**
     * @throws ParseErrorsWrapper
     */
    private static function parse(
        mixed $value,
        object $object
    ) : ObjectContext
    {
        $context = Parse::object(
            [
                "testBool" => $value,
                "testInt" => $value,
                "testFloat" => $value,
                "testString" => $value
            ],
            $object
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        return $context;
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetMessageNull()
    {
        $object = self::objectProvider();
        $this->expectExceptionMessage(
            <<<EOT
            4 errors in root object
                1 errors in element "testBool"
                    Element is null while it should be bool
                1 errors in element "testInt"
                    Element is null while it should be int
                1 errors in element "testFloat"
                    Element is null while it should be float
                1 errors in element "testString"
                    Element is null while it should be string
            
            EOT
        );
        self::parse(
            null,
            $object
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetMessageBool()
    {
        $object = self::objectProvider();
        self::parse(
            true,
            $object
        );

        $this->assertTrue(
            $object->testBool === true
        );
        $this->assertTrue(
            $object->testInt === 1
        );
        $this->assertTrue(
            $object->testFloat === 1.0
        );
        $this->assertTrue(
            $object->testString === "1"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetMessageInt()
    {
        $object = self::objectProvider();
        self::parse(
            2,
            $object
        );

        $this->assertTrue(
            $object->testBool === true
        );
        $this->assertTrue(
            $object->testInt === 2
        );
        $this->assertTrue(
            $object->testFloat === 2.0
        );
        $this->assertTrue(
            $object->testString === "2"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetMessageFloat()
    {
        $object = self::objectProvider();
        self::parse(
            2.5,
            $object
        );

        $this->assertTrue(
            $object->testBool === true
        );
        $this->assertTrue(
            $object->testInt === 2
        );
        $this->assertTrue(
            $object->testFloat === 2.5
        );
        $this->assertTrue(
            $object->testString === "2.5"
        );

        $this->expectExceptionMessage(
            <<<EOT
            1 errors in root object
                1 errors in element "testInt"
                    Element is float while it should be int
            
            EOT
        );
        self::parse(
            PHP_FLOAT_MAX,
            $object
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetMessageString()
    {
        $object = self::objectProvider();
        $this->expectExceptionMessage(
            <<<EOT
            2 errors in root object
                1 errors in element "testInt"
                    Element is string while it should be int
                1 errors in element "testFloat"
                    Element is string while it should be float
            
            EOT
        );
        self::parse(
            "jsioaf",
            $object
        );
    }

    public function testGetExpectedTypes()
    {
        $object = new class () {

            public array|self $testUnionTypesOfArrayAndClass;

            public ?bool $testNullableBool;

        };

        $context = Parse::object(
            [
                "testUnionTypesOfArrayAndClass" => null,
                "testNullableString" => ""
            ],
            $object
        );
        try {
            $context->copyToObject(
                $object,
                "root object"
            );
        } catch (ParseErrorsWrapper $parseError) {
        }
        if (!isset($parseError)) {
            throw new AssertionError(
                "No errors when copy parsed data to object"
            );
        }

        $properties = $context->getPropertyContexts();
        foreach ($properties as $property) {
            $treeKey = $property->getErrorsTreeKey();
            $tree = $parseError->getErrorsTree()[$treeKey];
            [$err] = $tree;

            if ($err instanceof TypeMismatchError) {
                $this->assertTrue(
                    $err->getExpectedTypes() === [
                        "array"
                    ]
                );
            } else {
                throw new AssertionError(
                    "Error is not " . TypeMismatchError::class
                );
            }
        }
    }
}
