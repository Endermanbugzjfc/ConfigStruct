<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext\BeforeParse;

use ReflectionProperty;

final class PropertyDetails
{

    public function __construct(
        protected ReflectionProperty $reflection,
        protected string $keyName
    )
    {
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return string
     */
    public function getKeyName() : string
    {
        return $this->keyName;
    }

}