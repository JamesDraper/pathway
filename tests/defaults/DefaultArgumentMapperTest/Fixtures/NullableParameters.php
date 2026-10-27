<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest\Fixtures;

final class NullableParameters
{
    public function handle(?string $first, ?string $second = null): void {}
}
