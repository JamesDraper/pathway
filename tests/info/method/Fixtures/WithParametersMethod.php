<?php

declare(strict_types=1);

namespace Tests\Info\Method\Fixtures;

final class WithParametersMethod
{
    public function greet(string $greeting, string ...$names): void
    {
        echo $greeting . ' ' . implode(', ', $names);
    }
}
