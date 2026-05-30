<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class ProtectedMethod
{
    protected function greetProtected(): void
    {
        echo 'Hello World!';
    }
}
