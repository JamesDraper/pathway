<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

final class FloatParameter
{
    public function addFour(float $f): float
    {
        return $f + 4.0;
    }
}
