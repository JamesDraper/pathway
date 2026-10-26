<?php

declare(strict_types=1);

namespace Tests\Defaults\Fixtures\DefaultHandlerLocator;

final class PrivateFactoryMethodHandler
{
    private static function make(): self
    {
        return new self();
    }
}
