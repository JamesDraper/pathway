<?php
declare(strict_types=1);

namespace Tests\Integration\Internal\Reflection;

use Pathway\Internal\Reflection\HandlerClassFactory;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerClassFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_handler_class(): void
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

        $result = $factory->create($handler);

        $this->assertSame('prepare', $result->prepare->getName());
        $this->assertSame($handler, $result->prepare->getHandler());

        $this->assertSame('process', $result->process->getName());
        $this->assertSame($handler, $result->process->getHandler());

        $this->assertSame('finalize', $result->finalize->getName());
        $this->assertSame($handler, $result->finalize->getHandler());
    }
}
