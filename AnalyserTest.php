<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use Endermanbugzjfc\ConfigStruct\attributes\KeyName;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

class AnalyserTest extends TestCase
{

    public function testAnalyseStruct()
    {

    }

    /**
     * @throws StructureException
     * @throws ReflectionException
     */
    public function testDoesKeyNameHaveDuplicatedArguments()
    {
        $struct = new class() {

            #[KeyName(
                "test key name duplicated arguments",
                "test key name duplicated arguments"
            )]
            public string $testKeyNameDuplicatedArguments;

        };
        $this->expectException(StructureException::class);
        Analyser::doesKeyNameHaveDuplicatedArguments(
            $struct,
            new ReflectionProperty(
                $struct,
                "testKeyNameDuplicatedArguments"
            )
        );
    }

    public function testWasKeyNameAlreadyUsed()
    {

    }

    public function testDoesGroupPropertyHasInvalidType()
    {

    }
}
