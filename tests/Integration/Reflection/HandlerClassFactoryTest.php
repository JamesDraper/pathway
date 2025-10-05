<?php
declare(strict_types=1);

namespace Tests\Integration\Reflection;

use Pathway\Reflection\HandlerClassFactory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HandlerClassFactoryTest extends TestCase
{
    #[Test]
    public function itCreatesAHandlerClass(): void
    {
        $handler = new class {
            public function prepare(int $a, int $b): int
            {
                return $a + $b;
            }

            public function process(int $c, int $d): int
            {
                return $c + $d;
            }

            public function finalize(int $e, int $f): int
            {
                return $e + $f;
            }
        };

        $factory = new HandlerClassFactory;

        $result = $factory($handler);

        $this->assertSame('prepare', $result->getPrepareMethod()->getName());
        $this->assertSame($handler, $result->getPrepareMethod()->getHandler());

        $this->assertSame('process', $result->getProcessMethod()->getName());
        $this->assertSame($handler, $result->getProcessMethod()->getHandler());

        $this->assertSame('finalize', $result->getFinalizeMethod()->getName());
        $this->assertSame($handler, $result->getFinalizeMethod()->getHandler());
    }
}
