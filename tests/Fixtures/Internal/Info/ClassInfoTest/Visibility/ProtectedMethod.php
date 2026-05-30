<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Visibility;

final class ProtectedMethod
{
    protected function greetProtected(): void
    {
        echo 'Hello World!';
    }
}
