<?php

declare(strict_types=1);

namespace Tests\Defaults\Fixtures\DefaultHandlerLocator;

final class ProtectedFactoryMethodHandler
{
    protected static function make(): self
    {
        return new self();
    }
}
