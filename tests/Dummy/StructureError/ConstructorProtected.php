<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\Dummy\StructureError;

final class ConstructorProtected
{

    protected function __construct()
    {
    }

    public static function create() : self
    {
        return new self();
    }

}