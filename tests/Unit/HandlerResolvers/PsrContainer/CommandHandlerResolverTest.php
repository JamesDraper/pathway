<?php
declare(strict_types=1);

namespace Pathway\Tests\HandlerResolvers\PsrContainer;

use Pathway\HandlerResolvers\PsrContainer\CommandHandlerResolver;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery;

use Psr\Container\ContainerInterface;

use LogicException;
use stdClass;

final class CommandHandlerResolverTest extends TestCase
{
    #[Test]
    public function it_resolves_a_handler_successfully(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $resolver = new CommandHandlerResolver($container);
        $handler = new stdClass();

        $container
            ->expects()
            ->has($handler::class . 'Handler')
            ->andReturn(true);

        $container
            ->expects()
            ->get($handler::class . 'Handler')
            ->andReturn($handler);

        $result = $resolver->resolve($handler::class);

        $this->assertSame($handler, $result);
    }

    #[Test]
    public function it_throws_an_exception_if_a_handler_id_is_not_in_the_container(): void
    {
        $this->expectException(LogicException::class);

        $container = Mockery::mock(ContainerInterface::class);
        $resolver = new CommandHandlerResolver($container);
        $handler = new stdClass();

        $container
            ->expects()
            ->has($handler::class . 'Handler')
            ->andReturn(false);

        $resolver->resolve($handler::class);
    }

    #[Test]
    public function it_throws_an_exception_if_a_handler_is_not_an_object(): void
    {
        $this->expectException(LogicException::class);

        $container = Mockery::mock(ContainerInterface::class);
        $resolver = new CommandHandlerResolver($container);
        $handler = new stdClass();

        $container
            ->expects()
            ->has($handler::class . 'Handler')
            ->andReturn(true);

        $container
            ->expects()
            ->get($handler::class . 'Handler')
            ->andReturn(123);

        $resolver->resolve($handler::class);
    }
}
