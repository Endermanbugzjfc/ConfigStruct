<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct;

use Exception;
use ReflectionClass;
use ReflectionProperty;
use TypeError;

/**
 * @template T of object
 */
final class StructParseContext
{
    private bool $finalized = false;

    /**
     * @internal
     * @throws RuntimeException
     */
    public function finalize() : void
    {
        if ($this->finalized) {
            throw new \RuntimeException("Finalization of " . self::class . " which had already been finalized");
        }
        $this->finalized = true;
    }

    /**
     * @param T $object
     * @param ReflectionClass<T> $class
     */
    public function __construct(
        private mixed $object,
        private ReflectionClass $class
    ) {
    }

    /**
     * @return T
     */
    public function getObject() : mixed
    {
        return $this->object;
    }

    /**
     * @return ReflectionClass<T>
     */
    public function getReflection() : ReflectionClass
    {
        return $this->class;
    }

    /**
     * @var ReflectionProperty[] Key = property name.
     * @phpstan-var array<string, ReflectionProperty>
     */
    private array $properties = [];

    /**
     * @return ReflectionProperty[] Key = property name.
     * @phpstan-return array<string, ReflectionProperty>
     */
    public function getProperties() : array
    {
        return $this->properties;
    }

    /**
     * @var bool[] Key = property name.
     * @phpstan-var array<string, bool>
     */
    private array $presences = [];

    /**
     * @return bool[] Key = property name.
     * @phpstan-return array<string, bool>
     */
    public function getPresences() : array
    {
        return $this->presences;
    }

    /**
     * @var ?TypeError[] Key = property name.
     * @phpstan-var array<string, TypeError|null>
     */
    private array $typeErrors = [];

    /**
     * @return ?TypeError[] Key = property name.
     * @phpstan-return array<string, TypeError|null>
     */
    public function getTypeErrors() : array
    {
        return $this->typeErrors;
    }

    /**
     * @throws Exception
     */
    public function __get(string $name) : mixed
    {
        if ($this->finalized) {
            throw new Exception("Please use getter functions");
        }

        return $this->$name;
    }

    /**
     * @throws Exception
     */
    public function __set(string $name, mixed $value) : void
    {
        if ($this->finalized) {
            throw new Exception("Modification to finalized " . self::class);
        }

        $this->$name = $value;
    }
}