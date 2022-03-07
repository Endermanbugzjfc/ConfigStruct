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

    public function testArrayUnsetRecursive()
    {
        $array = [
            "testAssociativeArray" => [
                "a" => "testUnset",
                "b" => "testChange",
                "c" => "testOmit"
            ],
            "testList" => [
                "testUnset",
                "testChange",
                "testOmit"
            ],
            2 => "testUnset",
            3 => "testChange",
            4 => "testOmit"
        ];

        ConfigStructUtils::arrayUnsetRecursive(
            $array,
            function (
                array $keys,
                mixed &$value
            ) : bool {
                if ($value === "testUnset") {
                    $this->assertTrue(
                        $keys === [
                            "testAssociativeArray",
                            "a"
                        ]
                        or
                        $keys === [
                            "testList",
                            0
                        ]
                        or
                        $keys === [
                            2
                        ]
                    );

                    return true;
                }
                if ($value === "testChange") {
                    $this->assertTrue(
                        $keys === [
                            "testAssociativeArray",
                            "b"
                        ]
                        or
                        $keys === [
                            "testList",
                            1
                        ]
                        or
                        $keys === [
                            3
                        ]
                    );

                    $value = "changed";
                }
                if ($value === "testOmit") {
                    $this->assertTrue(
                        $keys === [
                            "testAssociativeArray",
                            "c"
                        ]
                        or
                        $keys === [
                            "testList",
                            2
                        ]
                        or
                        $keys === [
                            4
                        ]
                    );
                }

                return false;
            }
        );

        $this->assertTrue(
            $array === [
                "testAssociativeArray" => [
                    "b" => "changed",
                    "c" => "testOmit"
                ],
                "testList" => [
                    "changed",
                    "testOmit"
                ],
                3 => "changed",
                4 => "testOmit"
            ]
        );
    }

}
