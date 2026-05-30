<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Variadic;

use function array_sum;

final class VariadicParameter
{
    public function sum(int ...$ints): int
    {
        return array_sum($str);
    }
}
