<?php
declare(strict_types=1);

namespace Tests\Unit\Internal;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\HandlerRunner\HandlerRunner;
use Pathway\DispatcherInterface;
use Pathway\Internal\Dispatcher;
use Pathway\Internal\Exception;

use Tests\Fixtures\Message1;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use stdClass;

final class DispatcherTest extends TestCase
{
    private readonly MockInterface&CommandHandlerResolverInterface $commandHandlerResolver;

    private readonly MockInterface&EventHandlerResolverInterface $eventHandlerResolver;

    private readonly MockInterface&HandlerRunnerFactory $handlerRunnerFactory;

    private readonly MockInterface&DispatcherInterface $dispatcherInterface;

    private Dispatcher $dispatcher;

    #[Test]
    public function it_dispatches_a_command(): void
    {
        $command = new Message1;
        $handler = new stdClass;

        $this
            ->commandHandlerResolver
            ->expects()
            ->resolve(Message1::class)
            ->andReturn($handler);

        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $handlerRunner
            ->expects()
            ->run($command, $this->dispatcherInterface)
            ->andReturn(123);

        $result = $this->dispatcher->command($command);

        $this->assertSame(123, $result);
    }

    #[Test]
    #[Depends('it_dispatches_a_command')]
    public function it_only_resolves_a_command_handler_once(): void
    {
        $command = new Message1;
        $handler = new stdClass;

        $this
            ->commandHandlerResolver
            ->expects()
            ->resolve(Message1::class)
            ->andReturn($handler);

        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $handlerRunner
            ->expects()
            ->run($command, $this->dispatcherInterface)
            ->twice()
            ->andReturn(123);

        $this->dispatcher->command($command);
        $this->dispatcher->command($command);
    }

    #[Test]
    public function it_dispatches_an_event(): void
    {
        $event = new Message1;
        $handler1 = new stdClass;
        $handler2 = new stdClass;

        $this
            ->eventHandlerResolver
            ->expects()
            ->resolve(Message1::class)
            ->andReturn([$handler1, $handler2]);

        $handlerRunner1 = Mockery::mock(HandlerRunner::class);
        $handlerRunner2 = Mockery::mock(HandlerRunner::class);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler1)
            ->andReturn($handlerRunner1);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler2)
            ->andReturn($handlerRunner2);

        $handlerRunner1
            ->expects()
            ->run($event, $this->dispatcherInterface)
            ->andReturn(123);

        $handlerRunner2
            ->expects()
            ->run($event, $this->dispatcherInterface)
            ->andReturn(456);

        $this->dispatcher->event($event);
    }

    #[Test]
    #[Depends('it_dispatches_an_event')]
    public function it_only_resolves_an_event_handler_once(): void
    {
        $event = new Message1;
        $handler1 = new stdClass;
        $handler2 = new stdClass;

        $this
            ->eventHandlerResolver
            ->expects()
            ->resolve(Message1::class)
            ->andReturn([$handler1, $handler2]);

        $handlerRunner1 = Mockery::mock(HandlerRunner::class);
        $handlerRunner2 = Mockery::mock(HandlerRunner::class);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler1)
            ->andReturn($handlerRunner1);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler2)
            ->andReturn($handlerRunner2);

        $handlerRunner1
            ->expects()
            ->run($event, $this->dispatcherInterface)
            ->twice()
            ->andReturn(123);

        $handlerRunner2
            ->expects()
            ->run($event, $this->dispatcherInterface)
            ->twice()
            ->andReturn(456);

        $this->dispatcher->event($event);
        $this->dispatcher->event($event);
    }

    #[Test]
    public function it_fails_if_an_event_resolver_does_not_return_objects(): void
    {
        $event = new Message1;
        $handler1 = new stdClass;
        $handler2 = 123;

        $this
            ->eventHandlerResolver
            ->expects()
            ->resolve(Message1::class)
            ->andReturn([$handler1, $handler2]);

        $handlerRunner1 = Mockery::mock(HandlerRunner::class);

        $this
            ->handlerRunnerFactory
            ->expects()
            ->make($handler1)
            ->andReturn($handlerRunner1);

        $handlerRunner1
            ->expects()
            ->run($event, $this->dispatcherInterface)
            ->andReturn(123);

        $this->assertThrown(
            Exception::eventHandlerNotObject(Message1::class, 1, 123),
            fn () => $this->dispatcher->event($event),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandHandlerResolver = Mockery::mock(CommandHandlerResolverInterface::class);
        $this->eventHandlerResolver = Mockery::mock(EventHandlerResolverInterface::class);
        $this->handlerRunnerFactory = Mockery::mock(HandlerRunnerFactory::class);
        $this->dispatcherInterface = Mockery::mock(DispatcherInterface::class);

        $this->dispatcher = new Dispatcher(
            $this->commandHandlerResolver,
            $this->eventHandlerResolver,
            $this->handlerRunnerFactory,
            $this->dispatcherInterface,
        );
    }
}
