<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

final class StaticMethod
{
    public static function run(int $number): int
    {
        return $number * 2;
    }
}
