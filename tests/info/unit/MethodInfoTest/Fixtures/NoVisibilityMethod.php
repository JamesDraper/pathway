<?php

declare(strict_types=1);

/**
 * @php-cs-fixer-ignore modifier_keywords
 */

namespace Tests\Info\Unit\MethodInfoTest\Fixtures;

final class NoVisibilityMethod
{
    // phpcs:ignore Squiz.Scope.MethodScope
    function greetNoVisibility(): void
    {
        echo 'Hello World!';
    }
}
