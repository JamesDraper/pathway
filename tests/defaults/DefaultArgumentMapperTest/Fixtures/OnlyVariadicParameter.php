<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest\Fixtures;

final class OnlyVariadicParameter
{
    public function handle(string ...$items): void {}
}
