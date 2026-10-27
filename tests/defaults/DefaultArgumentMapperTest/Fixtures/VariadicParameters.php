<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest\Fixtures;

final class VariadicParameters
{
    public function handle(string $first, string $second = 'second', string ...$rest): void {}
}
