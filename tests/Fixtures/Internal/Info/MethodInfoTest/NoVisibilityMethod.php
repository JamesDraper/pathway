<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\MethodInfoTest;

final class NoVisibilityMethod
{
    // // phpcs:ignore Squiz.Scope.MethodScope
    function greetNoVisibility(): void
    {
        echo 'Hello World!';
    }
}
