<?php
declare(strict_types=1);

namespace Tests\Info\Unit\MethodInfoTest\Fixtures;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
