<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

final class UntypedMethod
{
    public function run(mixed $value): mixed
    {
        return $value;
    }
}
