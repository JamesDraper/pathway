<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class PrivateMethod
{
    private function greetPrivate(): void
    {
        echo 'Hello World!';
    }
}
