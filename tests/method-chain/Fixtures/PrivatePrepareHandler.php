<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class PrivatePrepareHandler
{
    private function prepare(): void {} // @phpstan-ignore method.unused

    public function process(): void {}
}
