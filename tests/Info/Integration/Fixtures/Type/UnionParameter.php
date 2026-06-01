<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

final class UnionParameter
{
    public function addFive(string|int $value): int
    {
        return ((int) $i) + 5;
    }
}
