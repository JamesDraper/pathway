<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class NoVisibilityMethod
{
    function greetNoVisibility(): void
    {
        echo 'Hello World!';
    }
}
