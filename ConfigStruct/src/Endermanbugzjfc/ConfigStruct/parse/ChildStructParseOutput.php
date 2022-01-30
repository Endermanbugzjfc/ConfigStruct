<?php

namespace Endermanbugzjfc\ConfigStruct\parse;

final class ChildStructParseOutput extends ParseOutput
{

    private function __construct(
        protected StructParseOutput $childStruct
    )
    {
    }

    public static function create(
        StructParseOutput $childStruct
    ) : self
    {
        return new self(
            $childStruct
        );
    }

    /**
     * @return StructParseOutput
     */
    public function getChildStruct() : StructParseOutput
    {
        return $this->childStruct;
    }

    protected function getFlattenedValue() : object
    {
        return $this->getChildStruct()->copyValuesToNewObject();
    }

}