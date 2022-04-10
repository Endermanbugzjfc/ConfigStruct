<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\ConvertCase;
use Endermanbugzjfc\ConfigStruct\Utils\StaticClassTrait;
use ReflectionClass;
use ReflectionProperty;
use function is_array;
use function is_object;
use function is_scalar;

final class Emit
{
    use StaticClassTrait;

    /**
     * Convert an object into array. Base on its structure, which are the value types and attributes provided.
     *
     * Non-public, non-initialized and static properties are always ignored.
     *
     * This function is recursive. Child objects and arrays will also be emitted. However, this function will not handle recursive objects properly. Your application will slowly suffer and die from segmentation fault once there is a recursive object. So, it is your job to prevent this from happening!
     *
     * @return array Array key = property name or key name from the fist {@link KeyName} attribute if there is any.
     */
    public static function object(
        object $object
    ) : array {
        $reflect = new ReflectionClass(
            $object
        );
        $convertCase = $reflect->getAttributes(ConvertCase::class)[0] ?? null;
        $caseConverter = $convertCase?->getArguments()[0];
        foreach (
            $reflect->getProperties(
                ReflectionProperty::IS_PUBLIC
            ) as $property
        ) {
            if (!$property->isInitialized(
                $object
            )) {
                continue;
            }
            $keyName = $property->getAttributes(
                    KeyName::class
                )[0] ?? null;
            $name = $keyName?->getArguments()[0]
                ?? $property->getName();
            if ($caseConverter !== null) {
                $name = $caseConverter($name);
            }

            $value = $property->getValue(
                $object
            );
            $value = self::value(
                $value
            );
            $return[$name] = $value;
        }

        return $return ?? [];
    }

    /**
     * Redirect to the correct emit function. Base on the value's type and attributes provided.
     * @param mixed $value Value of the property.
     */
    public static function value(
        mixed $value
    ) : string|int|bool|array|float|null {
        if (is_object(
            $value
        )) {
            return self::object(
                $value
            );
        }

        if (is_array(
            $value
        )) {
            foreach ($value as $key => $item) {
                $return[$key] = self::value(
                    $item
                );
            }
            return $return ?? [];
        }

        if (
            $value !== null
            and
            !is_scalar(
                $value
            )
        ) {
            return (array)$value;
        }

        return $value;
    }
}