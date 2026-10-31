<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrivateFinaliseHandler
{
    private function finalise(): void {} // @phpstan-ignore method.unused

    public function process(): void {}
}
