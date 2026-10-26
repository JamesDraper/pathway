<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

final class PrivateFactoryMethodHandler
{
    private static function make(): self
    {
        return new self();
    }
}
