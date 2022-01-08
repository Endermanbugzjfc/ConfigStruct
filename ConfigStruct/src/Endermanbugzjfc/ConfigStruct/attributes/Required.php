<?php

namespace Endermanbugzjfc\ConfigStruct\attributes;

use Attribute;
use Endermanbugzjfc\ConfigStruct\exceptions\SetupException;
use LogLevel;
use pocketmine\Server;

#[Attribute(Attribute::TARGET_PROPERTY)] class Required
{

    public const THROW_EXCEPTION = [self::class, "throwException"];
    public const LOG = [self::class, "log"];

    public function __construct(
        callable $onMissing,
        ?string  $plugin = null,
        bool     $soft = false,
        string   $logLevel = LogLevel::ERROR
    )
    {
    }

    /**
     * @throws SetupException
     */
    public static function throwException(
        string  $field,
        ?string $file,
        ?string $plugin,
        bool    $soft,
        string  $logLevel
    ) : void
    {
        // Plugin can can be seen from the stacktrace, so it is not included here.
        throw new SetupException(
            "Required field \"$field\" is missing"
            . (isset($file) ? " in $file" : "")
        );
    }

    public static function log(
        string  $field,
        ?string $file,
        ?string $plugin,
        bool    $soft,
        string  $logLevel
    ) : void
    {
        Server::getInstance()
            ->getPluginManager()
            ->getPlugin($plugin)
            ->getLogger()
            ->log(
                $logLevel, "Required field \"$field\" is missing"
                . (isset($file) ? " in $file" : "")
            );
    }


}