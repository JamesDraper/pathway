<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

use RuntimeException;

final class OptionalRuntimeExceptionMethod
{
    public function run(int $first, int $second = 2): never
    {
        throw new RuntimeException('Something optional went wrong.');
    }
}
