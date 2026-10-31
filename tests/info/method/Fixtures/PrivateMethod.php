<?php

declare(strict_types=1);

namespace Tests\Info\Method\Fixtures;

final class PrivateMethod
{
    private function greetPrivate(): void // @phpstan-ignore method.unused
    {
        echo 'Hello World!';
    }
}
