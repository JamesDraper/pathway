<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Type;

final class BoolParameter
{
    public function not(bool $b): bool
    {
        return !$b;
    }
}
