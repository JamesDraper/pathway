<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class CommandOnlyHandler
{
    public function process(Command $command): string
    {
        return $command->value;
    }
}
