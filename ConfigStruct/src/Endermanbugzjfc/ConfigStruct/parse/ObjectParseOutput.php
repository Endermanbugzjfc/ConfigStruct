<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\StructureError;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;
use TypeError;

final class ObjectParseOutput
{

    /**
     * @param ReflectionClass $reflection
     * @param PropertyParseOutput[] $propertiesOutput Key = property name.
     * @param array $unhandledElements Raw value of elements in the input which do not have the corresponding property.
     * @param ReflectionProperty[] $missingElements Key = property name.
     */
    public function __construct(
        protected ReflectionClass $reflection,
        protected array           $propertiesOutput,
        protected array           $unhandledElements,
        protected array           $missingElements
    )
    {
    }

    /**
     * @return ReflectionClass
     */
    public function getReflection() : ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array Raw value of elements in the input which do not have the corresponding property.
     */
    public function getUnhandledElements() : array
    {
        return $this->unhandledElements;
    }

    /**
     * @return ReflectionProperty[] Key = property name.
     */
    public function getMissingElements() : array
    {
        return $this->missingElements;
    }

    /**
     * @return PropertyParseOutput[] Key = property name.
     */
    public function getPropertiesOutput() : array
    {
        return $this->propertiesOutput;
    }

    /**
     * Copy output data to the given object.
     * @param object $object This object will be modified.
     * @return Throwable[] Errors (mostly {@link TypeError}). Key = property name.
     */
    public function copyToObject(
        object $object
    ) : array
    {
        $properties = $this->getPropertiesOutput();
        foreach ($properties as $name => $property) {
            try {
                $property->getReflection()->setValue(
                    $object,
                    $property->getValue()
                );
            } catch (Throwable $err) {
                $errs[$name] = $err;
            }
        }
        return $errs ?? [];
    }

    /**
     * Copy output data to an new object.
     * @return object The constructor of object should have 0 arguments.
     * @throws StructureError Failed to construct a new instance (probably incompatible arguments).
     */
    public function copyToNewObject(
        ?array &$errs = null
    ) : object
    {
        try {
            $instance = $this->getReflection()->newInstance();
        } catch (ReflectionException $err) {
            throw new StructureError($err);
        }
        $errs = $this->copyToObject(
            $instance
        );
        return $instance;
    }

}