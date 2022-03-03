<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use RuntimeException;

trait ExtendingContextTrait
{

    /**
     * Please use {@link self::create()} instead.
     */
    private function __construct()
    {
    }

    public function getValue() : mixed {
        throw new RuntimeException(
            "Implement me"
        );
    }

    public static function create() : self {
        throw new RuntimeException(
            "Implement me"
        );
    }

}