<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class ProtectedFinaliseHandler
{
    protected function finalise(): void {}

    public function process(): void {}
}
