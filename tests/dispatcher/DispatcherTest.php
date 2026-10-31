<?php

declare(strict_types=1);

namespace Tests\Dispatcher;

use Pathway\Resolution\HandlerIdentifier;
use Pathway\HandlerDefinitionException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Resolution\HandlerLocator;
use Pathway\DispatcherInterface;
use Pathway\Dispatcher;

use Tests\Dispatcher\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use RuntimeException;

final class DispatcherTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(Dispatcher::class);
    }

    #[Test]
    public function it_implements_the_dispatcher_interface(): void
    {
        $this->assertImplementsInterface(Dispatcher::class, DispatcherInterface::class);
    }

    #[Test]
    public function it_runs_the_command_through_the_handlers_method_chain(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\Handler();

        $dispatcher = $this->makeDispatcher(['handler' => $handler]);

        $result = $dispatcher->dispatch($command);

        $this->assertSame('finalised', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $dispatcher]],
            ['method' => 'process', 'arguments' => ['prepared']],
            ['method' => 'finalise', 'arguments' => ['processed']],
        ], $handler->calls);
    }

    #[Test]
    public function it_identifies_the_handler_from_the_command_class(): void
    {
        $handlerIdentifier = new Fixtures\RecordingHandlerIdentifier([Fixtures\Command::class => 'handler']);

        $dispatcher = new Dispatcher(
            $handlerIdentifier,
            new Fixtures\RecordingHandlerLocator(['handler' => new Fixtures\Handler()]),
            new Fixtures\RecordingArgumentMapper(),
        );

        $dispatcher->dispatch(new Fixtures\Command());

        $this->assertSame([Fixtures\Command::class], $handlerIdentifier->calls);
    }

    #[Test]
    public function it_caches_the_handler_id_for_each_command_class(): void
    {
        $handlerIdentifier = new Fixtures\RecordingHandlerIdentifier([
            Fixtures\Command::class => 'handler',
            Fixtures\OtherCommand::class => 'other',
        ]);

        $dispatcher = new Dispatcher(
            $handlerIdentifier,
            new Fixtures\RecordingHandlerLocator([
                'handler' => new Fixtures\Handler(),
                'other' => new Fixtures\Handler(),
            ]),
            new Fixtures\RecordingArgumentMapper(),
        );

        $dispatcher->dispatch(new Fixtures\Command());
        $dispatcher->dispatch(new Fixtures\Command());
        $dispatcher->dispatch(new Fixtures\OtherCommand());
        $dispatcher->dispatch(new Fixtures\OtherCommand());

        $this->assertSame([Fixtures\Command::class, Fixtures\OtherCommand::class], $handlerIdentifier->calls);
    }

    #[Test]
    public function it_locates_each_handler_once(): void
    {
        $handler = new Fixtures\Handler();
        $handlerLocator = new Fixtures\RecordingHandlerLocator(['handler' => $handler]);

        $dispatcher = new Dispatcher(
            new Fixtures\RecordingHandlerIdentifier([
                Fixtures\Command::class => 'handler',
                Fixtures\OtherCommand::class => 'handler',
            ]),
            $handlerLocator,
            new Fixtures\RecordingArgumentMapper(),
        );

        $first = new Fixtures\Command();
        $second = new Fixtures\OtherCommand();

        $dispatcher->dispatch($first);
        $dispatcher->dispatch($second);

        $this->assertSame(['handler'], $handlerLocator->calls);
        $this->assertSame($first, $handler->calls[0]['arguments'][0]);
        $this->assertSame($second, $handler->calls[3]['arguments'][0]);
    }

    #[Test]
    public function it_passes_the_argument_mapper_to_the_method_chain(): void
    {
        $argumentMapper = new Fixtures\RecordingArgumentMapper();

        $this->makeDispatcher(['handler' => new Fixtures\Handler()], $argumentMapper)->dispatch(new Fixtures\Command());

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
            ['method' => 'finalise', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_method_chain(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Handler class "' . Fixtures\NoProcessHandler::class . '" does not have a "process" method.',
        );

        $this->makeDispatcher(['handler' => new Fixtures\NoProcessHandler()])->dispatch(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_handler_identifier(): void
    {
        $handlerIdentifier = new class implements HandlerIdentifier {
            public function identify(string $class): string
            {
                throw new RuntimeException('Cannot identify.');
            }
        };

        $dispatcher = new Dispatcher(
            $handlerIdentifier,
            new Fixtures\RecordingHandlerLocator([]),
            new Fixtures\RecordingArgumentMapper(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot identify.');

        $dispatcher->dispatch(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_handler_locator(): void
    {
        $handlerLocator = new class implements HandlerLocator {
            public function locate(string $id): object
            {
                throw new RuntimeException('Cannot locate.');
            }
        };

        $dispatcher = new Dispatcher(
            new Fixtures\RecordingHandlerIdentifier([Fixtures\Command::class => 'handler']),
            $handlerLocator,
            new Fixtures\RecordingArgumentMapper(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot locate.');

        $dispatcher->dispatch(new Fixtures\Command());
    }

    /**
     * @param array<string, object> $handlers
     */
    private function makeDispatcher(array $handlers, ?ArgumentMapper $argumentMapper = null): Dispatcher
    {
        return new Dispatcher(
            new Fixtures\RecordingHandlerIdentifier([Fixtures\Command::class => 'handler']),
            new Fixtures\RecordingHandlerLocator($handlers),
            $argumentMapper ?? new Fixtures\RecordingArgumentMapper(),
        );
    }
}
