<?php
declare(strict_types=1);

namespace Tests\Dispatcher;

use Pathway\HandlerResolvers\CommandHandlerResolverInterface;
use Pathway\HandlerResolvers\EventHandlerResolverInterface;
use Pathway\Internal\HandlerRunner\HandlerRunnerFactory;
use Pathway\Internal\HandlerRunner\HandlerRunner;
use Pathway\Internal\Dispatcher\Resolver;
use Pathway\Internal\Exception;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use LogicException;
use stdClass;

final class ResolverTest extends TestCase
{
    private readonly MockInterface&CommandHandlerResolverInterface $commandResolver;

    private readonly MockInterface&EventHandlerResolverInterface $eventResolver;

    private readonly MockInterface&HandlerRunnerFactory $factory;

    private readonly Resolver $resolver;

    #[Test]
    public function it_resolves_a_command_handler(): void
    {
        $handler = new stdClass();
        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->commandResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn($handler);

        $this
            ->factory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $result = $this->resolver->command(stdClass::class);

        $this->assertSame($handlerRunner, $result);
    }

    #[Test]
    #[Depends('it_resolves_a_command_handler')]
    public function it_only_resolves_a_command_handler_once(): void
    {
        $handler = new stdClass();
        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->commandResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn($handler);

        $this
            ->factory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $result1 = $this->resolver->command(stdClass::class);
        $result2 = $this->resolver->command(stdClass::class);

        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function it_resolves_an_event_handler(): void
    {
        $handler1 = new stdClass();
        $handlerRunner1 = Mockery::mock(HandlerRunner::class);

        $handler2 = new stdClass();
        $handlerRunner2 = Mockery::mock(HandlerRunner::class);

        $handler3 = new stdClass();
        $handlerRunner3 = Mockery::mock(HandlerRunner::class);

        $this
            ->eventResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn([
                $handler1,
                $handler2,
                $handler3,
            ]);

        $this
            ->factory
            ->expects()
            ->make($handler1)
            ->andReturn($handlerRunner1);

        $this
            ->factory
            ->expects()
            ->make($handler2)
            ->andReturn($handlerRunner2);

        $this
            ->factory
            ->expects()
            ->make($handler3)
            ->andReturn($handlerRunner3);

        $results = $this->resolver->event(stdClass::class);

        $this->assertSame([$handlerRunner1, $handlerRunner2, $handlerRunner3], $results);
    }

    #[Test]
    #[Depends('it_resolves_an_event_handler')]
    public function it_resolves_an_event_handler_once(): void
    {
        $handler = new stdClass();
        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->eventResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn([
                $handler,
            ]);

        $this
            ->factory
            ->expects()
            ->make($handler)
            ->andReturn($handlerRunner);

        $results1 = $this->resolver->event(stdClass::class);
        $results2 = $this->resolver->event(stdClass::class);

        $this->assertSame($results1, $results2);
    }

    #[Test]
    #[Depends('it_resolves_an_event_handler')]
    public function it_throws_an_exception_if_an_event_handler_is_not_an_object(): void
    {
        $handler1 = new stdClass();
        $handlerRunner = Mockery::mock(HandlerRunner::class);

        $this
            ->eventResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn([
                $handler1,
                123,
            ]);

        $this
            ->factory
            ->expects()
            ->make($handler1)
            ->andReturn($handlerRunner);

        $this->assertThrown(
            Exception::eventHandlerNotObject(stdClass::class, 1, 123),
            fn () => $this->resolver->event(stdClass::class),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandResolver = Mockery::mock(CommandHandlerResolverInterface::class);
        $this->eventResolver = Mockery::mock(EventHandlerResolverInterface::class);
        $this->factory = Mockery::mock(HandlerRunnerFactory::class);

        $this->resolver = new Resolver(
            $this->commandResolver,
            $this->eventResolver,
            $this->factory,
        );
    }
}
