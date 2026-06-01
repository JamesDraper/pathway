<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\DefaultValue;

use function strrev;

final class DefaultValueParameter
{
    public function greet(string $user): void
    {
        echo 'Hello ' . $user;
    }
}
