<?php
declare(strict_types=1);

namespace Tests\Unit\Internal;

use Pathway\Resolvers\CommandHandlerResolverInterface;
use Pathway\Resolvers\EventHandlerResolverInterface;
use Pathway\Internal\Reflection\HandlerClassFactory;
use Pathway\Internal\Reflection\HandlerClass;
use Pathway\Internal\Resolver;

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

    private readonly MockInterface&HandlerClassFactory $factory;

    private readonly Resolver $resolver;

    #[Test]
    public function itResolvesACommandHandler(): void
    {
        $handler = new stdClass();
        $handlerClass = Mockery::mock(HandlerClass::class);

        $this
            ->commandResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn($handler);

        $this
            ->factory
            ->expects()
            ->create($handler)
            ->andReturn($handlerClass);

        $result = $this->resolver->command(stdClass::class);

        $this->assertSame($handlerClass, $result);
    }

    #[Test]
    #[Depends('itResolvesACommandHandler')]
    public function itOnlyResolvesACommandHandlerOnce(): void
    {
        $handler = new stdClass();
        $handlerClass = Mockery::mock(HandlerClass::class);

        $this
            ->commandResolver
            ->expects()
            ->resolve(stdClass::class)
            ->andReturn($handler);

        $this
            ->factory
            ->expects()
            ->create($handler)
            ->andReturn($handlerClass);

        $result1 = $this->resolver->command(stdClass::class);
        $result2 = $this->resolver->command(stdClass::class);

        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function itResolvesAnEventHandler(): void
    {
        $handler1 = new stdClass();
        $handlerClass1 = Mockery::mock(HandlerClass::class);

        $handler2 = new stdClass();
        $handlerClass2 = Mockery::mock(HandlerClass::class);

        $handler3 = new stdClass();
        $handlerClass3 = Mockery::mock(HandlerClass::class);

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
            ->create($handler1)
            ->andReturn($handlerClass1);

        $this
            ->factory
            ->expects()
            ->create($handler2)
            ->andReturn($handlerClass2);

        $this
            ->factory
            ->expects()
            ->create($handler3)
            ->andReturn($handlerClass3);

        $results = $this->resolver->event(stdClass::class);

        $this->assertSame([$handlerClass1, $handlerClass2, $handlerClass3], $results);
    }

    #[Test]
    #[Depends('itResolvesAnEventHandler')]
    public function itResolvesAnEventHandlerOnce(): void
    {
        $handler = new stdClass();
        $handlerClass = Mockery::mock(HandlerClass::class);

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
            ->create($handler)
            ->andReturn($handlerClass);

        $results1 = $this->resolver->event(stdClass::class);
        $results2 = $this->resolver->event(stdClass::class);

        $this->assertSame($results1, $results2);
    }

    #[Test]
    #[Depends('itResolvesAnEventHandler')]
    public function itThrowsAnExceptionIfAnEventHandlerIsNotAnObject(): void
    {
        $this->expectException(LogicException::class);

        $handler1 = new stdClass();
        $handlerClass1 = Mockery::mock(HandlerClass::class);

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
            ->create($handler1)
            ->andReturn($handlerClass1);

        $this->resolver->event(stdClass::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandResolver = Mockery::mock(CommandHandlerResolverInterface::class);
        $this->eventResolver = Mockery::mock(EventHandlerResolverInterface::class);
        $this->factory = Mockery::mock(HandlerClassFactory::class);

        $this->resolver = new Resolver(
            $this->commandResolver,
            $this->eventResolver,
            $this->factory,
        );
    }
}
