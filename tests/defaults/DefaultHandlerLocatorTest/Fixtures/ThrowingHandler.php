<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

use RuntimeException;

final class ThrowingHandler
{
    public static function make(): self
    {
        throw new RuntimeException('Something went wrong.');
    }
}
