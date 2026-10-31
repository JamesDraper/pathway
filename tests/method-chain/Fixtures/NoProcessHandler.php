<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class NoProcessHandler
{
    public function prepare(): void {}

    public function finalize(): void {}
}
