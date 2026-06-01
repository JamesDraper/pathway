<?php
declare(strict_types=1);

namespace Tests\Info\Unit\MethodInfoTest\Fixtures;

final class StaticMethod
{
    public static function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
