<?php

namespace Endermanbugzjfc\ConfigStruct\struct;

interface StructHolderInterface
{

    public function newStructForParsing() : object;

    public function newStructForEmitting() : object;

}