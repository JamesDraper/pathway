<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrivateFinalizeHandler
{
    private function finalize(): void {} // @phpstan-ignore method.unused

    public function process(): void {}
}
