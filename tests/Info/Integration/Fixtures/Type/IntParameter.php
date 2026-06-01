<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

final class IntParameter
{
    public function addThree(int $i): int
    {
        return $i + 3;
    }
}
