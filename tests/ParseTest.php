<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use Endermanbugzjfc\ConfigStruct\Dummy\Extending\ConflictWithA;
use Endermanbugzjfc\ConfigStruct\Dummy\RecursiveChildObject;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use function array_map;

class ParseTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectPropertyAccessLevels()
    {
        $object = new class() {

            private ?bool $testPrivateProperty;

            protected ?bool $testProtectedProperty;

            public ?bool $testPublicProperty;
        };

        $keyName = "testPublicProperty";
        $context = Parse::object(
            $object,
            [
                "testPrivateProperty" => null,
                "testProtectedProperty" => null,
                $keyName => null
            ]
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        $this->assertTrue(
            $context->getPropertyContexts()[$keyName]->getDetails()->getKeyName()
            === $keyName
        );

        [
            $private,
            $protected,
            $public
        ] = $context->getReflection()->getProperties();
        $private->setAccessible(true);
        $protected->setAccessible(true);
        $this->assertNotTrue($private->isInitialized($object));
        $this->assertNotTrue($protected->isInitialized($object));
        $this->assertTrue($public->isInitialized($object));
        $this->assertTrue($public->getValue($object) === null);
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectUnhandledElements()
    {
        $object = new class() {

        };

        $context = Parse::object(
            $object,
            [
                "testA" => "testA",
                null => "",
                0
            ]
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        $this->assertTrue(
            $context->getUnhandledElements() === [
                "testA" => "testA",
                null => "",
                0
            ]
        );
    }

    public function testObjectMissingElements()
    {
        $object = new class() {

            public bool $testNoDefaultValue;

            public bool $testDefaultValue = true;

        };
        $context = Parse::object(
            $object,
            [

            ]
        );

        $this->assertTrue(
            $context->getMissingElements()
            ["testNoDefaultValue"]
                ->getName() === "testNoDefaultValue"
        );
        $this->assertTrue(
            $context->getMissingElements()
            ["testDefaultValue"]
                ->getName() === "testDefaultValue"
        );
    }

    /**
     * @throws ParseErrorsWrapper
     */
    public function testObjectChildStructRecursive()
    {
        $object = new RecursiveChildObject();
        $class = $object::class;
        $context = Parse::object(
            $object,
            $object::dataSampleA()
        );
        $context->copyToObject(
            $object,
            "root object"
        );

        $this->assertTrue(
            $object->testA === "testA"
        );

        $oneDeeper = $object->testSelf;
        $this->assertTrue(
            $oneDeeper instanceof $class
        );
        $this->assertTrue(
            $oneDeeper->testA === "testB"
        );

        $twoDeeper = $oneDeeper->testSelf;
        $this->assertTrue(
            $twoDeeper instanceof $class
        );
        $this->assertTrue(
            !isset($twoDeeper->testA)
        );
        $this->assertTrue(
            !isset($twoDeeper->testSelf)
        );
    }

    /**
     * @param string[] $candidates
     * @return ReflectionClass[]
     * @throws ReflectionException
     */
    private static function classNamesToReflections(
        array $candidates
    ) : array
    {
        return array_map(
            fn(string $class) : ReflectionClass => new ReflectionClass(
                $class
            ),
            $candidates
        );
    }

    /**
     * @throws Exception
     */
    public function testFindMatchingStructSuccess()
    {
        $candidates = self::classNamesToReflections(
            [
                A::class,
                ConflictWithA::class
            ]
        );

        $findA = Parse::findMatchingStruct(
            $candidates,
            [
                "a" => ""
            ]
        );
        $this->assertTrue(
            $findA->getReflection()->getName()
            === A::class
        );

        $findConflictWithA = Parse::findMatchingStruct(
            $candidates,
            [
                "a" => [
                ]
            ]
        );
        $this->assertTrue(
            $findConflictWithA->getReflection()->getName()
            === ConflictWithA::class
        );
    }

    /**
     * @throws Exception
     */
    public function testFindMatchingStructFailure()
    {
        $candidates = self::classNamesToReflections(
            [
                A::class,
                ConflictWithA::class
            ]
        );

        $firstErr = Parse::findMatchingStruct(
            $candidates,
            [
                "a" => [
                    "a" => ""
                ]
            ]
        );
        $this->assertTrue(
            $firstErr instanceof ParseErrorsWrapper
        );
    }

}
