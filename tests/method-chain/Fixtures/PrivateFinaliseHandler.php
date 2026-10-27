<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrivateFinaliseHandler
{
    private function finalise(): void {}

    public function process(): void {}
}
