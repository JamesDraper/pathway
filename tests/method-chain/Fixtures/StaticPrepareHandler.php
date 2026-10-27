<?php

declare(strict_types=1);

namespace Tests\MethodChain\Fixtures;

final class StaticPrepareHandler
{
    public static function prepare(): void {}

    public function process(): void {}
}
