<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Attribute;
use Exception;
use ReflectionProperty;

/**
 * Emit and parse this property with the name provided in the attribute instead of the property name.
 *
 * If multiple names are provided, only the first one is used for emitting, and the first name available in the input (the first key that exists in the parse data) is used for parsing.
 *
 * Name duplication will cause a {@link Exception} at TODO time.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rename
{
    /**
     * @param array<int|string> $moreNames
     */
    public function __construct(
        public int|string $name,
        public array $moreNames = [],
        public bool $omitConvertCase = false
    ) {
        $checked = [];
        $moreNames[] = $name;
        foreach ($moreNames as $moreName) {
            if (isset($checked[$moreName])) {
                throw new Exception("Rename attribute has one or more duplicated names");
            }
            $checked[$moreName] = true;
        }
    }

    public static function get(ReflectionProperty $property) : ?self
    {
        $attribute = $property->getAttributes(self::class)[0] ?? null;
        if ($attribute === null) {
            return null;
        }

        $arguments = $attribute->getArguments();
        $self = new self(...$arguments);
        return $self;
    }
}