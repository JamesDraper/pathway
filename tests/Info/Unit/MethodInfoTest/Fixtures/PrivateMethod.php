<?php
declare(strict_types=1);

namespace Tests\Info\Unit\MethodInfoTest\Fixtures;

final class PrivateMethod
{
    private function greetPrivate(): void
    {
        echo 'Hello World!';
    }
}
