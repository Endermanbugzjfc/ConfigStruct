<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

use Endermanbugzjfc\ConfigStruct\KeyName;
use ReflectionProperty;
use function array_key_exists;

final class ParseTimeProperty
{

    protected mixed $boundKeyName;

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

    public function bindKeyNameIgnoreExistenceInData(
        array $data
    ) : string
    {
        foreach ($this->getKeyNameCandidates() as $name) {
            if (array_key_exists($name, $data)) {
                return $this->boundKeyName = $name;
            }
        }
        return $this->getReflection()->getName();
    }

    /**
     * @return mixed
     */
    public function getBoundKeyName() : mixed
    {
        return $this->boundKeyName;
    }

    /**
     * @param mixed $boundKeyName
     */
    public function setBoundKeyName(mixed $boundKeyName) : void
    {
        $this->boundKeyName = $boundKeyName;
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