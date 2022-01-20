<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

use Endermanbugzjfc\ConfigStruct\Analyse;
use Endermanbugzjfc\ConfigStruct\exceptions\StructureError;
use ReflectionClass;
use ReflectionException;

final class StructHolder implements StructHolderInterface
{

    /**
     * @throws ReflectionException
     * @throws StructureError
     */
    private function __construct(
        protected string $class
    )
    {
        Analyse::struct(new ReflectionClass($this->class), []);
    }

    /**
     * @throws StructureError
     * @throws ReflectionException
     */
    public static function newStructHolder(object $struct) : self
    {
        return new self($struct::class);
    }

    public function newStructForParsing() : object
    {
        return new $this->class;
    }

    public function newStructForEmitting() : object
    {
        return new $this->class;
    }

}