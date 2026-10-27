<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class MismatchedProcessHandler
{
    public function prepare(Command $command): string
    {
        return $command->value;
    }

    public function process(int $value): int
    {
        return $value;
    }
}
