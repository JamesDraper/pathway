<?php
declare(strict_types=1);

namespace Tests\HandlerResolvers\InMemory;

use Pathway\HandlerResolvers\InMemory\CommandHandlerResolver;
use Pathway\HandlerResolvers\InMemory\Exception;

use Tests\HandlerResolvers\Fixtures\Message0;
use Tests\HandlerResolvers\Fixtures\Message1;
use Tests\HandlerResolvers\Fixtures\Message2;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use stdClass;

final class CommandHandlerResolverTest extends TestCase
{
    private readonly CommandHandlerResolver $resolver;

    private readonly stdClass $handler;

    #[Test]
    public function it_resolves_a_command_handler(): void
    {
        $result = $this->resolver->resolve(Message1::class);

        $this->assertSame($this->handler, $result);
    }

    #[Test]
    public function it_throws_an_exception_if_command_handler_missing(): void
    {
        $this->assertThrown(Exception::noHandlerForCommand(Message0::class), function (): void {
            $this->resolver->resolve(Message0::class);
        });
    }

    #[Test]
    public function it_throws_an_exception_if_command_handler_not_object(): void
    {
        $this->assertThrown(Exception::commandHandlerNotObject(Message2::class, 123), function (): void {
            $this->resolver->resolve(Message2::class);
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new stdClass;

        $this->resolver = new CommandHandlerResolver([ // @phpstan-ignore argument.type
            Message1::class => $this->handler,
            Message2::class => 123,
        ]);
    }
}
