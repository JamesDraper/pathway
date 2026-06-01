<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ParameterInfoTest\Fixtures;

use function strrev;

final class SimpleParameter
{
    public function reverse(string $str): string
    {
        return strrev($str);
    }
}
