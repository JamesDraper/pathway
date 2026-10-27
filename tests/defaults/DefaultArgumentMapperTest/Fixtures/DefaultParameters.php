<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest\Fixtures;

final class DefaultParameters
{
    public function handle(string $first, string $second = 'second', string $third = 'third'): void {}
}
