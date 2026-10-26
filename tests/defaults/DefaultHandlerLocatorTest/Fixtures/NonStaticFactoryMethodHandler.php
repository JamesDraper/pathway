<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

final class NonStaticFactoryMethodHandler
{
    public function make(): self
    {
        return new self();
    }
}
