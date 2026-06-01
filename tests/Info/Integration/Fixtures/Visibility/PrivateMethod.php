<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Visibility;

final class PrivateMethod
{
    private function greetPrivate(): void
    {
        echo 'Hello World!';
    }
}
