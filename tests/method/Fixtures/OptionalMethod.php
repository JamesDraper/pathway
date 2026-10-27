<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

final class OptionalMethod
{
    public function run(int $first, int $second = 2): int
    {
        return $first + $second;
    }
}
