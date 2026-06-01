<?php
declare(strict_types=1);

namespace Tests\Info\MethodInfoTest\Fixtures;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
