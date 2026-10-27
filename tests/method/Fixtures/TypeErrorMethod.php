<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

use TypeError;

final class TypeErrorMethod
{
    public function run(int $number): never
    {
        throw new TypeError('Something inside went wrong.');
    }
}
