<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
