<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Visibility;

final class ProtectedMethod
{
    protected function greetProtected(): void
    {
        echo 'Hello World!';
    }
}
