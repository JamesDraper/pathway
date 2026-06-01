<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

final class BoolParameter
{
    public function not(bool $b): bool
    {
        return !$b;
    }
}
