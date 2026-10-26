<?php

declare(strict_types=1);

namespace Tests\Defaults\Fixtures\DefaultHandlerLocator;

final class InvalidReturnHandler
{
    public static mixed $returnValue = null;

    public static function make(): mixed
    {
        return self::$returnValue;
    }
}
