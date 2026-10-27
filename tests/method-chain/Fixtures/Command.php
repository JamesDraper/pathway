<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class Command
{
    public function __construct(
        public readonly string $value = 'command',
    ) {}
}
