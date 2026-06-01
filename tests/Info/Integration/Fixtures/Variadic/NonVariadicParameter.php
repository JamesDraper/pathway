<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Variadic;

use function array_sum;

final class NonVariadicParameter
{
    /**
     * @param list<int> $ints
     */
    public function sum(array $ints): int
    {
        return array_sum($str);
    }
}
