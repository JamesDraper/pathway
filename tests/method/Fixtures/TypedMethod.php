<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

final class TypedMethod
{
    public function run(int $number, string $text): string
    {
        return $number . $text;
    }
}
