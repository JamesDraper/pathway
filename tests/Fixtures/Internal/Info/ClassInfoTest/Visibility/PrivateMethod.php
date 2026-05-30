<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Visibility;

final class PrivateMethod
{
    private function greetPrivate(): void
    {
        echo 'Hello World!';
    }
}
