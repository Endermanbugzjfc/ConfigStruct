<?php


declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseOutput;

use ReflectionProperty;
use Throwable;
use function array_values;

final class ListParseOutput extends PropertyParseOutput
{

    /**
     * @param string $keyName
     * @param ReflectionProperty $reflection
     * @param Throwable[] $errors
     * @param ObjectParseOutput[] $objectParseOutput
     */
    public function __construct(
        string             $keyName,
        ReflectionProperty $reflection,
        array              $errors,
        protected array    $objectParseOutput
    )
    {
        parent::__construct(
            $keyName,
            $reflection,
            $errors
        );
    }

    public function getValue() : array
    {
        foreach (
            $this->getObjectParseOutput()
            as $key => $output
        ) {
            $return[$key] = $output->copyToNewObject();
        }
        return $return ?? [];
    }

    /**
     * @return ObjectParseOutput[]
     */
    public function getObjectParseOutput() : array
    {
        return $this->objectParseOutput;
    }

    public function isAssociative() : bool
    {
        return array_values(
                $this->objectParseOutput
            ) === $this->objectParseOutput;
    }

}