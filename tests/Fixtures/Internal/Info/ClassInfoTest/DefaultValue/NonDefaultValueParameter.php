<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\DefaultValue;

use function strrev;

final class DefaultValueParameter
{
    public function greet(string $user): void
    {
        echo 'Hello ' . $user;
    }
}
