<?php

use pocketmine\utils\Config;

class ConfigStruct
{

    private function __construct()
    {
    }

    public static function parse(
        string $file,
        object $struct,
        int    $type = Config::DETECT
    ) : ?Throwable
    {
    }

    public static function emit(
        string $file,
        object $struct,
        int    $type = Config::DETECT
    ) : ?Throwable
    {
    }

}