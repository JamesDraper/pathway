<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ParameterInfoTest\Fixtures;

use function implode;

final class VariadicParameter
{
    public function concatenate(string ...$strs): string
    {
        return implode(' ', $strs);
    }
}
