<?php
declare(strict_types=1);

namespace Tests;

use Pathway\Internal\Throwable;

use Mockery\Adapter\Phpunit\MockeryTestCase;

abstract class TestCase extends MockeryTestCase
{
    final protected function assertThrown(Throwable $throwable, callable $next): void
    {
        try {
            $next();
        } catch (Throwable $thrown) {
            $this->assertInstanceOf($throwable::class, $thrown);
            $this->assertSame($throwable->snapshot(), $thrown->snapshot());

            return;
        }

        $this->fail(sprintf('Expected %s to be thrown.', $throwable::class));
    }
}
