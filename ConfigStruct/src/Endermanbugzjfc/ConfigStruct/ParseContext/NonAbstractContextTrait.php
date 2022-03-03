<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use RuntimeException;
use Throwable;

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
     * @param BasePropertyContext $context
     * @param Throwable[] $errors
     */
    public static function create(
        BasePropertyContext $context,
        array               $errors
    )
    {
        throw new RuntimeException(
            "Implement me"
        );
    }

}