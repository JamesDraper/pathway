<?php
declare(strict_types=1);

namespace Tests\Unit\HandlerResolvers\InMemory;

use Pathway\HandlerResolvers\InMemory\EventHandlerResolver;
use Pathway\HandlerResolvers\InMemory\Exception;

use Tests\Fixtures\Message0;
use Tests\Fixtures\Message1;
use Tests\Fixtures\Message2;
use Tests\Fixtures\Message3;
use Tests\Fixtures\Message4;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use stdClass;

final class EventHandlerResolverTest extends TestCase
{
    private readonly EventHandlerResolver $resolver;

    private readonly stdClass $handler1;

    private readonly stdClass $handler2;

    #[Test]
    public function it_resolves_an_event_handler_list(): void
    {
        $results = $this->resolver->resolve(Message1::class);

        $this->assertSame([$this->handler1, $this->handler2], $results);
    }

    #[Test]
    public function it_resolves_an_empty_array_if_event_not_in_map(): void
    {
        $results = $this->resolver->resolve(Message0::class);

        $this->assertSame([], $results);
    }

    #[Test]
    public function it_throws_an_exception_if_event_handler_mapping_is_not_an_array(): void
    {
        $this->assertThrown(Exception::eventHandlerNotArray(Message2::class, 123), function (): void {
            $this->resolver->resolve(Message2::class);
        });
    }

    #[Test]
    public function it_throws_an_exception_if_event_handler_mapping_is_a_list(): void
    {
        $this->assertThrown(Exception::eventHandlerArrayNotList(Message3::class), function (): void {
            $this->resolver->resolve(Message3::class);
        });
    }

    #[Test]
    public function it_throws_an_exception_if_event_handler_list_contains_non_object(): void
    {
        $this->assertThrown(Exception::eventHandlerNotObject(Message4::class, 1, 123), function (): void {
            $this->resolver->resolve(Message4::class);
        });
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler1 = new stdClass;
        $this->handler2 = new stdClass;

        $this->resolver = new EventHandlerResolver([ // @phpstan-ignore argument.type
            Message1::class => [
                $this->handler1,
                $this->handler2,
            ],
            Message2::class => 123,
            Message3::class => [
                'one' => 'two',
            ],
            Message4::class => [
                new stdClass,
                123,
            ],
        ]);
    }
}
