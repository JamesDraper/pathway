<?php
declare(strict_types=1);

namespace Tests;

use Pathway\CommandHandlerResolver;
use Pathway\EventHandlerResolver;
use Pathway\DispatcherInterface;
use Pathway\Dispatcher;

use PHPUnit\Framework\Attributes\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;

final class DispatcherTest extends MockeryTestCase
{
    #[Test]
    public function it_implements_the_dispatcher_interface(): void
    {
        $dispatcher = new Dispatcher(
            Mockery::mock(CommandHandlerResolver::class),
            Mockery::mock(EventHandlerResolver::class),
        );

        $this->assertInstanceOf(DispatcherInterface::class, $dispatcher);
    }

    #[Test]
    public function it_disptches_commands(): void
    {
        $commandHandlerResolver = Mockery::mock(CommandHandlerResolver::class);

        $eventHandlerResolver = Mockery::mock(EventHandlerResolver::class);

        $dispatcher = new Dispatcher($commandHandlerResolver, $eventHandlerResolver);

        $command = new class () {
        };

        $handler = Mockery::mock();

        $commandHandlerResolver
            ->expects()
            ->resolve($command::class)
            ->andReturn($handler);

        $handler
            ->expects()
            ->prepare($command, $dispatcher)
            ->andReturn(['prepared']);

        $handler
            ->expects()
            ->process('prepared')
            ->andReturn(['processed']);

        $handler
            ->expects()
            ->finalize('processed')
            ->andReturn(['finalized']);

        $result = $dispatcher->command($command);

        $this->assertSame(['finalized'], $result);
    }

        #[Test]
    public function it_disptches_events(): void
    {
        $commandHandlerResolver = Mockery::mock(CommandHandlerResolver::class);

        $eventHandlerResolver = Mockery::mock(EventHandlerResolver::class);

        $dispatcher = new Dispatcher($commandHandlerResolver, $eventHandlerResolver);

        $event = new class () {
        };

        $handler1 = Mockery::mock();
        $handler2 = Mockery::mock();

        $eventHandlerResolver
            ->expects()
            ->resolve($event::class)
            ->andReturn([$handler1, $handler2]);

        $handler1
            ->expects()
            ->prepare($event, $dispatcher)
            ->andReturn(['prepared1']);

        $handler1
            ->expects()
            ->process('prepared1')
            ->andReturn(['processed1']);

        $handler1
            ->expects()
            ->finalize('processed1');

        $handler2
            ->expects()
            ->prepare($event, $dispatcher)
            ->andReturn(['prepared2']);

        $handler2
            ->expects()
            ->process('prepared2')
            ->andReturn(['processed2']);

        $handler2
            ->expects()
            ->finalize('processed2');

        $dispatcher->event($event);
    }
}
