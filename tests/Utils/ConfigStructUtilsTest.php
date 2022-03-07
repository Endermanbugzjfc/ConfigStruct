<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Utils;

use PHPUnit\Framework\TestCase;

class ConfigStructUtilsTest extends TestCase
{

    public function testArrayIsList()
    {
        $arrayWithDigitButStringKeys = [
            "0" => "a",
            "1" => "b",
            "2" => "c"
        ];
        $this->assertTrue( // Classic PHP.
            ConfigStructUtils::arrayIsList(
                $arrayWithDigitButStringKeys
            )
        );

        $arrayWithDisorderedIntKeys = [
            1 => "a",
            0 => "b",
            2 => "c"
        ];
        $this->assertFalse(
            ConfigStructUtils::arrayIsList(
                $arrayWithDisorderedIntKeys
            )
        );
    }

    public function testArrayDiffRecursive()
    {
        $array = [
            "a" => [
                "b",
                "c"
            ],
            "d"
        ];

        $diff = ConfigStructUtils::arrayDiffRecursive(
            $array,
            [
                "a" => [
                    "b"
                ],
                "d"
            ]
        );

        $this->assertTrue(
            $diff === [
                "a" => [
                    1 => "c"
                ]
            ]
        );
    }

}
