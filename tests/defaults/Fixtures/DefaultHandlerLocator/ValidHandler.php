<?php

declare(strict_types=1);

namespace Tests\Defaults\Fixtures\DefaultHandlerLocator;

final class ValidHandler
{
    public static function make(): self
    {
        return new self();
    }
}
