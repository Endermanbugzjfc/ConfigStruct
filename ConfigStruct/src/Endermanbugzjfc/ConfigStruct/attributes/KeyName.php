<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use ReflectionProperty;

/**
 * Emit and parse this property with the name provided in the attribute instead of the property name.
 *
 * If multiple names are provided, only the first one is used for emitting, and the first name available in the input is used for parsing. This gets useful when updating configs.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class KeyName
{

    public function __construct(
        int|string $name,
    )
    {
    }

    /**
     * @param array $array The input to be scanned.
     * @param ReflectionProperty $property Property's {@link KeyName} attribute will be used in the search. Return null if it doesn't have.
     * @return int|string|null Null = no first name available.
     */
    public static function searchFirstNameAvailable(
        array              $array,
        ReflectionProperty $property
    ) : int|string|null
    {
        $attribute = $property->getAttributes(self::class)[0] ?? null;
        if ($attribute === null) {
            return null;
        }

        foreach ($attribute->getArguments() as $sn) {
            if (array_key_exists($sn, $array)) {
                $name = $sn;
                break;
            }
        }
        return $name ?? null;
    }

}