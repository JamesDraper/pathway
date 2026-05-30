<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class StaticMethod
{
    public static function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
