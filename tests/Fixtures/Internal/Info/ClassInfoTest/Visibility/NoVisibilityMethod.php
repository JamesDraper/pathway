<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Visibility;

final class NoVisibilityMethod
{
    function greetNoVisibility(): void
    {
        echo 'Hello World!';
    }
}
