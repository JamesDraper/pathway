<?php
declare(strict_types=1);

namespace Tests\HandlerRunner;

use Pathway\HandlerRunner\HandlerRunnerFactory;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerRunnerFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_handler_runner_class(): void
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

        $factory = new HandlerRunnerFactory;

        $result = $factory->make($handler);

        $this->assertSame('prepare', $result->prepare->name);
        $this->assertSame($handler, $result->prepare->handler);

        $this->assertSame('process', $result->process->name);
        $this->assertSame($handler, $result->process->handler);

        $this->assertSame('finalize', $result->finalize->name);
        $this->assertSame($handler, $result->finalize->handler);
    }
}
