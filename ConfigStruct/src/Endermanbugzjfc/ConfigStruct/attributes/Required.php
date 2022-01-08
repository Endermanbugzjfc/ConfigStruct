<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\SetupException;

#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public const THROW_EXCEPTION = [self::class, "throwException"];

    public function __construct(callable $onMissing)
    {
    }

    /**
     * @throws SetupException
     */
    public static function throwException(string $field, ?string $file = null) : void
    {
        throw new SetupException(
            "Required field \"$field\" is missing"
            . (isset($file) ? " in $file" : "")
        );
    }

}