<?php
declare(strict_types=1);

namespace Tests\Dispatcher;

use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\HandlerRunner\HandlerRunner;
use Pathway\Internal\Dispatcher\Dispatcher;
use Pathway\Internal\Dispatcher\Resolver;
use Pathway\DispatcherInterface;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class DispatcherTest extends TestCase
{
    private readonly Dispatcher $dispatcher;

    #[Test]
    public function it_dispatches_a_command(): void
    {
        $handler = new stdClass;
        $command = new stdClass;

        $this
            ->dispatcher
            ->resolver
            ->expects()
            ->command($command::class)
            ->andReturn($handler);

        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->dispatcher
            ->handlerRunnerFactory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $handlerRunner
            ->expects()
            ->run($command, $this->dispatcher->dispatcher)
            ->andReturn(123);

        $result = $this->dispatcher->command($command);

        $this->assertSame(123, $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new Dispatcher(
            Mockery::mock(DispatcherInterface::class),
            Mockery::mock(Resolver::class),
            Mockery::mock(HandlerRunnerFactory::class),
        );
    }
}
