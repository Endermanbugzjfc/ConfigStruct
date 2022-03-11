<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ConfigStruct\ParseContext;

use Endermanbugzjfc\ConfigStruct\Dummy\RecursiveChildObject;
use Endermanbugzjfc\ConfigStruct\Parse;
use Endermanbugzjfc\ConfigStruct\ParseErrorsWrapper;
use PHPUnit\Framework\TestCase;

class ChildObjectContextTest extends TestCase
{

    /**
     * @throws ParseErrorsWrapper
     */
    public function testGetUnhandledElements()
    {
        $object = new RecursiveChildObject();
        $context = Parse::object(
            $object,
            $object::dataSampleA()
        );
        $context->copyToObject(
            $object,
            "root object"
        );
        $child = $context->getPropertyContexts()["testSelf"];

        $this->assertTrue(
            $child->getUnhandledElements() === [
                "testUnhandledElement" => null
            ]
        );
    }
}
