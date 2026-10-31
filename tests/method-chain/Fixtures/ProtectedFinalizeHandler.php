<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class ProtectedFinalizeHandler
{
    protected function finalize(): void {}

    public function process(): void {}
}
