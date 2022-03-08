<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use PHPUnit\Framework\TestCase;

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
    public function testGetMessageNull()
    {
        $object = self::objectProvider();
        $context = Parse::object(
            [
                "testBool" => null,
                "testInt" => null,
                "testFloat" => null,
                "testString" => null
            ],
            $object
        );
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
        $context->copyToObject(
            $object,
            "root object"
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
