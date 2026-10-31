<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

use RuntimeException;

final class VariadicRuntimeExceptionMethod
{
    public function run(int ...$numbers): never
    {
        throw new RuntimeException('Something variadic went wrong.');
    }
}
