<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Dummy\StructureError;

use Endermanbugzjfc\ConfigStruct\Dummy\Extending\A;
use Endermanbugzjfc\ConfigStruct\Dummy\Extending\B;
use Endermanbugzjfc\ConfigStruct\Dummy\Extending\Base;
use Endermanbugzjfc\ConfigStruct\ListType;

class DuplicatedStructCandidates
{
    #[ListType(self::class)] #[ListType(DuplicatedStructCandidates::class)]
    #[ListType(A::class)] #[ListType(A::class)]
    #[ListType(B::class)] #[ListType(B::class)]
    // Abstract classes should be ignored.
    #[ListType(Base::class)] #[ListType(Base::class)]
    public array $testThreeDuplicatedListTypes;
}