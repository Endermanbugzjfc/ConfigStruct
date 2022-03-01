<?php

namespace Endermanbugzjfc\ConfigStruct;

use Endermanbugzjfc\ConfigStruct\utils\StaticClassTrait;
use ReflectionClass;
use ReflectionProperty;
use function is_object;

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
            if (is_object(
                $value
            )) {
                $value = self::object($value);
            }
            // TODO: Array
            $return[$name] = $value;
        }

        return $return ?? [];
    }

}