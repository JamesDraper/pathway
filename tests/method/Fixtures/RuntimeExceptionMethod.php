<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

use RuntimeException;

final class RuntimeExceptionMethod
{
    public function run(int $number): never
    {
        throw new RuntimeException('Something went wrong.');
    }
}
