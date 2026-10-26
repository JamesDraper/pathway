<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;

class ParentHandler
{
    public static function make(): self
    {
        return new ChildHandler();
    }
}
