<?php

declare(strict_types=1);

namespace Tests\Dispatcher;

use Pathway\Internal\MethodChainException;
use Pathway\Resolution\HandlerIdentifier;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Resolution\HandlerLocator;
use Pathway\DispatcherInterface;
use Pathway\Info\MethodInfo;
use Pathway\Dispatcher;

use Tests\Dispatcher\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use RuntimeException;

use function array_is_list;
use function is_array;

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

        $result = $this->makeDispatcher(['handler' => $handler])->dispatch($command);

        $this->assertSame('finalised', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $handler]],
            ['method' => 'process', 'arguments' => ['prepared']],
            ['method' => 'finalise', 'arguments' => ['processed']],
        ], $handler->calls);
    }

    #[Test]
    public function it_identifies_the_handler_from_the_command_class(): void
    {
        $handlerIdentifier = $this->makeHandlerIdentifier([Fixtures\Command::class => 'handler']);

        $dispatcher = new Dispatcher(
            $handlerIdentifier,
            $this->makeHandlerLocator(['handler' => new Fixtures\Handler()]),
            $this->makeArgumentMapper(),
        );

        $dispatcher->dispatch(new Fixtures\Command());

        $this->assertSame([Fixtures\Command::class], $handlerIdentifier->calls);
    }

    #[Test]
    public function it_caches_the_handler_id_for_each_command_class(): void
    {
        $handlerIdentifier = $this->makeHandlerIdentifier([
            Fixtures\Command::class => 'handler',
            Fixtures\OtherCommand::class => 'other',
        ]);

        $dispatcher = new Dispatcher(
            $handlerIdentifier,
            $this->makeHandlerLocator([
                'handler' => new Fixtures\Handler(),
                'other' => new Fixtures\Handler(),
            ]),
            $this->makeArgumentMapper(),
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
        $handlerLocator = $this->makeHandlerLocator(['handler' => $handler]);

        $dispatcher = new Dispatcher(
            $this->makeHandlerIdentifier([
                Fixtures\Command::class => 'handler',
                Fixtures\OtherCommand::class => 'handler',
            ]),
            $handlerLocator,
            $this->makeArgumentMapper(),
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
        $argumentMapper = $this->makeArgumentMapper();

        $this->makeDispatcher(['handler' => new Fixtures\Handler()], $argumentMapper)->dispatch(new Fixtures\Command());

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
            ['method' => 'finalise', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_method_chain(): void
    {
        $this->expectException(MethodChainException::class);
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

        $dispatcher = new Dispatcher($handlerIdentifier, $this->makeHandlerLocator([]), $this->makeArgumentMapper());

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
            $this->makeHandlerIdentifier([Fixtures\Command::class => 'handler']),
            $handlerLocator,
            $this->makeArgumentMapper(),
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
            $this->makeHandlerIdentifier([Fixtures\Command::class => 'handler']),
            $this->makeHandlerLocator($handlers),
            $argumentMapper ?? $this->makeArgumentMapper(),
        );
    }

    /**
     * Records each call and returns the class's id from the given map.
     *
     * @param array<class-string, string> $ids
     */
    private function makeHandlerIdentifier(array $ids): HandlerIdentifier
    {
        return new class ($ids) implements HandlerIdentifier {
            /**
             * @var list<string>
             */
            public array $calls = [];

            /**
             * @param array<class-string, string> $ids
             */
            public function __construct(
                private readonly array $ids,
            ) {}

            public function identify(string $class): string
            {
                $this->calls[] = $class;

                return $this->ids[$class];
            }
        };
    }

    /**
     * Records each call and returns the handler for the id from the given map.
     *
     * @param array<string, object> $handlers
     */
    private function makeHandlerLocator(array $handlers): HandlerLocator
    {
        return new class ($handlers) implements HandlerLocator {
            /**
             * @var list<string>
             */
            public array $calls = [];

            /**
             * @param array<string, object> $handlers
             */
            public function __construct(
                private readonly array $handlers,
            ) {}

            public function locate(string $id): object
            {
                $this->calls[] = $id;

                return $this->handlers[$id];
            }
        };
    }

    /**
     * Records each call. A list is returned untouched; anything else is wrapped in a list, so it is passed as the
     * method's only argument.
     */
    private function makeArgumentMapper(): ArgumentMapper
    {
        return new class implements ArgumentMapper {
            /**
             * @var list<array{method: string, value: mixed}>
             */
            public array $calls = [];

            public function resolve(MethodInfo $methodInfo, mixed $value): array
            {
                $this->calls[] = ['method' => $methodInfo->getName(), 'value' => $value];

                return is_array($value) && array_is_list($value) ? $value : [$value];
            }
        };
    }
}
