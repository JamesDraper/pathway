<?php

declare(strict_types=1);

namespace Tests\Info\Parameter\Fixtures;

final class DefaultValueParameter
{
    public function greet(string $user = 'guest'): void
    {
        echo 'Hello ' . $user;
    }
}
