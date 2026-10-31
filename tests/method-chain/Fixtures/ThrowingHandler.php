<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

use RuntimeException;

final class ThrowingHandler
{
    public function process(Command $command): never
    {
        throw new RuntimeException('Something went wrong.');
    }
}
