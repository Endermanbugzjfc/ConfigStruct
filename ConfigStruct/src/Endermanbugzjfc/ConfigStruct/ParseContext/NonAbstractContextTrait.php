<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use RuntimeException;

trait NonAbstractContextTrait
{

    /**
     * Please use {@link self::create()} instead.
     */
    private function __construct()
    {
    }

    /**
     * If you this message, means this method is unavailable.
     */
    public function getValue()
    {
        throw new RuntimeException(
            "Implement me"
        );
    }

    /**
     * If you this message, means this method is unavailable.
     */
    public static function create(
        BasePropertyContext $context
    )
    {
        throw new RuntimeException(
            "Implement me"
        );
    }

}