<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;
use Endermanbugzjfc\ConfigStruct\ParseError\TypeMismatchError;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ListType
{
    public const TYPE_BOOL = "bool";
    public const TYPE_INT = "int";
    public const TYPE_FLOAT = "float";
    public const TYPE_STRING = "string";

    /**
     * Struct candidate for elements in a list.
     *
     * If multiple candidates are provided, only the one with 0 errors and least unhandled elements will be used. If there is no available struct for an element, the first one will be used. (And receive an {@link TypeMismatchError}).
     * @param string $type Type / class name of the struct candidate. If a candidate's class becomes invalid during runtime, it will be omitted.
     */
    public function __construct( // @phpstan-ignore-line TODO: Make public property.
        string $type
    ) {
    }
}