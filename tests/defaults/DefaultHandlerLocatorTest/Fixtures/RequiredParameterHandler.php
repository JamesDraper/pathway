<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

final class RequiredParameterHandler
{
    public static function make(string $required): self
    {
        return new self();
    }
}
