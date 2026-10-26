<?php

declare(strict_types=1);

namespace Tests\Defaults\Fixtures\DefaultHandlerLocator;

class ParentHandler
{
    public static function make(): self
    {
        return new ChildHandler();
    }
}
