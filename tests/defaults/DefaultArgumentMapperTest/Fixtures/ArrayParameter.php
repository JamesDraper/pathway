<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest\Fixtures;

final class ArrayParameter
{
    /**
     * @param list<string> $items
     */
    public function handle(array $items): void {}
}
