<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Data;

use Endermanbugzjfc\ConfigStruct\StructTrait;

class Basic
{
    use StructTrait;

    private $private;
    protected $protected;

    public $untyped;
    public mixed $mixed;

    public bool $bool;
    public int $int;
    public float $float;
    public string $string;

    public ?bool $boolNullable;
    public ?int $intNullable;
    public ?float $floatNullable;
    public ?string $stringNullable;

    public bool $false = false;
    public bool $true = true;
    public int $negativeOne = -1;
    public int $negativeZero = -0;
    public int $zero = 0;
    public int $one = 1;
    public int $two = 2;
    public float $pointZero = .0;
    public float $onePointTwo = 1.2;
    public float $inf = INF;
    public float $nan = NAN;
    public string $empty = "";
    public string $notEmpty = "bmV2ZXIgZ29ubmEgZ2l2ZSB5b3UgdXA=
";
}