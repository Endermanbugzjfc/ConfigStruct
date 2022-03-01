<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionClass;
use ReflectionProperty;
use function is_array;
use function is_object;
use function is_scalar;

final class Emit
{
    use StaticClassTrait;

    /**
     * Convert an object into array. Base on its structure, which are the property types and attributes provided.
     *
     * Non-public, non-initialized and static properties are always ignored.
     *
     * This function is recursive. Child objects and arrays will also be emitted. However, this function will not handle recursive objects properly. Your application will slowly suffer and die from segmentation fault once there is a recursive object. So, it is your job to prevent this from happening!
     *
     * @param object $object
     * @return array Array key = property name or key name from the fist {@link KeyName} attribute if there is any.
     */
    public static function object(
        object $object
    ) : array
    {
        $reflect = new ReflectionClass($object);
        foreach (
            $reflect->getProperties(
                ReflectionProperty::IS_PUBLIC
            ) as $property
        ) {
            if (!$property->isInitialized()) {
                continue;
            }
            $name = $property->getAttributes(
                    KeyName::class
                )[0]?->getArguments()[0]
                ?? $property->getName();

            $value = $property->getValue(
                $object
            );
            $value = self::property(
                $property,
                $value
            );
            $return[$name] = $value;
        }

        return $return ?? [];
    }

    /**
     * Redirect to the correct emit function base on the value's type and attributes provided.
     * @param mixed $value Value of the property.
     * @return mixed
     */
    public static function property(
        mixed              $value
    ) : mixed
    {
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
                $return[$key] = self::property(
                    $item
                );
            }
            return $return ?? [];
        }

        if (!is_scalar(
            $value
        )) {
            return (array)$value;
        }

        return $value;
    }

}