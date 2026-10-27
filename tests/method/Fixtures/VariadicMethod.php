<?php

declare(strict_types=1);

namespace Tests\Method\Fixtures;

final class VariadicMethod
{
    /**
     * @return list<int>
     */
    public function run(string $label, int ...$numbers): array
    {
        return $numbers;
    }
}
