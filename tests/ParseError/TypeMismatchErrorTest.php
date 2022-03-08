<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseError;

use AssertionError;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use PHPUnit\Framework\TestCase;

class TypeMismatchErrorTest extends TestCase
{

    public function testGetMessage()
    {

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
