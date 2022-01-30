<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use ReflectionProperty;

final class ParseTimeProperty
{

    private function __construct(
        protected ParseTimeStruct    $owner,
        protected ReflectionProperty $reflection
    )
    {

    }

    public static function fromReflection(
        ParseTimeStruct    $owner,
        ReflectionProperty $reflection
    ) : self
    {
        return new self($owner, $reflection);
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflection() : ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return ParseTimeStruct
     */
    public function getOwner() : ParseTimeStruct
    {
        return $this->owner;
    }

    public function getKeyNameCandidates() : array
    {
        foreach ($this->getReflection()->getAttributes(
            KeyName::class
        ) as $keyName) {
            $return = $keyName->getArguments()[0];
        }
        return $return ?? [];
    }

}