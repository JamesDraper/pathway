<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifierException;
use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifier;
use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;
use Pathway\Internal\TypeFormatter;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DefaultEventHandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultEventHandlerIdentifier::class);
    }

    #[Test]
    public function it_implements_the_event_handler_indentifier_interface(): void
    {
        $this->assertChildOf(DefaultEventHandlerIdentifier::class, EventHandlerIdentifier::class);
    }

    #[Test]
    public function it_returns_the_handlers(): void
    {
        $defaultEventHandlerIdentifier = new DefaultEventHandlerIdentifier([
            'Class\\Path\\One' => [
                'handler.one',
                'handler.two',
            ],
            'Class\\Path\\Two' => [
                'handler.three',
                'handler.four',
            ],
        ]);

        $result = $defaultEventHandlerIdentifier->identify('Class\\Path\\One');

        $this->assertSame([
            'handler.one',
            'handler.two',
        ], $result);
    }

    #[Test]
    public function it_returns_no_handlers_if_none_are_defined_for_the_class(): void
    {
        $defaultEventHandlerIdentifier = new DefaultEventHandlerIdentifier([
            'Class\\Path\\One' => [
                'handler.one',
                'handler.two',
            ],
            'Class\\Path\\Two' => [
                'handler.three',
                'handler.four',
            ],
        ]);

        $result = $defaultEventHandlerIdentifier->identify('Class\\Path\\Three');

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_throws_an_exception_if_a_handler_id_is_not_a_string(): void
    {
        $this->expectException(DefaultEventHandlerIdentifierException::class);
        $this->expectExceptionMessage('Events must be resolved to string identifiers, got TYPE.');

        $typeFormatter = $this->makeMock(TypeFormatter::class);

        $typeFormatter
            ->expects()
            ->format(123)
            ->andReturn('TYPE');

        $defaultEventHandlerIdentifier = $this->makePartialMock(
            DefaultEventHandlerIdentifier::class,
            [
                [
                    'Class\\Path\\One' => [
                        'handler.one',
                        'handler.two',
                    ],
                    'Class\\Path\\Two' => [
                        'handler.three',
                        123,
                    ],
                ],
            ],
        );

        $defaultEventHandlerIdentifier
            ->expects()
            ->makeTypeFormatter()
            ->andReturn($typeFormatter);

        $defaultEventHandlerIdentifier->identify('Class\\Path\\Two');
    }
}
