<?php
declare(strict_types=1);

namespace Tests\Info\MethodInfoTest\Fixtures;

final class ProtectedMethod
{
    protected function greetProtected(): void
    {
        echo 'Hello World!';
    }
}
