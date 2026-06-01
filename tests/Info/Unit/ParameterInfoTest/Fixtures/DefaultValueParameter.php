<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ParameterInfoTest\Fixtures;

final class DefaultValueParameter
{
    public function greet(string $user = 'guest'): void
    {
        echo 'Hello ' . $user;
    }
}
