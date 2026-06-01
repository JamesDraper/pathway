<?php
declare(strict_types=1);

namespace Tests\Info\MethodInfoTest\Fixtures;

final class NoVisibilityMethod
{
    // // phpcs:ignore Squiz.Scope.MethodScope
    function greetNoVisibility(): void
    {
        echo 'Hello World!';
    }
}
