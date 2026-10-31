<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrivateProcessHandler
{
    private function process(): void {} // @phpstan-ignore method.unused
}
