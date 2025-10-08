<?php
declare(strict_types=1);

namespace Tests;

use Pathway\Throwable;

use Mockery;

abstract class TestCase extends \PHPUnit\Framework\TestCase
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

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
