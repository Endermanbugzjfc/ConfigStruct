<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Dummy;

final class RecursiveChildObject
{

    public string $testA;

    public self $testSelf;

    public static function dataSampleA() : array
    {
        return [
            "testA" => "testA",
            "testSelf" => [
                "testA" => "testB",
                "testSelf" => [
                ],
                "testUnhandledElement" => null
            ]
        ];
    }

}