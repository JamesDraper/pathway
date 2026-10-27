<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class ProtectedPrepareHandler
{
    protected function prepare(): void {}

    public function process(): void {}
}
