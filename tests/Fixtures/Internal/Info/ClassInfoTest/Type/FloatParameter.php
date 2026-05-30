<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Type;

final class FloatParameter
{
    public function addFour(float $f): float
    {
        return $f + 4.0;
    }
}
