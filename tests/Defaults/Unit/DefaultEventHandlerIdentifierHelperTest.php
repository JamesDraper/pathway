<?php
declare(strict_types=1);

namespace Tests\Defaults\Unit;

use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifierException;
use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifier;
use Pathway\Internal\Defaults\DefaultEventHandlerIdentifierHelper;
use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;
use Pathway\Internal\TypeFormatter;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DefaultEventHandlerIdentifierHelperTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultEventHandlerIdentifierHelper::class);
    }

    #[Test]
    public function it_returns_the_handlers(): void
    {
        $typeFormatter = $this->makeMock(TypeFormatter::class);
        $defaultEventHandlerIdentifierHelper = new DefaultEventHandlerIdentifierHelper($typeFormatter, [
            'Class\\Path\\One' => [
                'handler.one',
                'handler.two',
            ],
            'Class\\Path\\Two' => [
                'handler.three',
                'handler.four',
            ],
        ]);

        $result = $defaultEventHandlerIdentifierHelper->identify('Class\\Path\\One');

        $this->assertSame([
            'handler.one',
            'handler.two',
        ], $result);
    }

    #[Test]
    public function it_returns_no_handlers_if_none_are_defined_for_the_class(): void
    {
        $typeFormatter = $this->makeMock(TypeFormatter::class);
        $defaultEventHandlerIdentifierHelper = new DefaultEventHandlerIdentifierHelper($typeFormatter, [
            'Class\\Path\\One' => [
                'handler.one',
                'handler.two',
            ],
            'Class\\Path\\Two' => [
                'handler.three',
                'handler.four',
            ],
        ]);

        $result = $defaultEventHandlerIdentifierHelper->identify('Class\\Path\\Three');

        $this->assertSame([], $result);
    }

    #[Test]
    public function it_throws_an_exception_if_a_handler_id_is_not_a_string(): void
    {
        $this->expectException(DefaultEventHandlerIdentifierException::class);
        $this->expectExceptionMessage('Events must be resolved to string identifiers, got TYPE.');

        $typeFormatter = $this->makeMock(TypeFormatter::class);

        $defaultEventHandlerIdentifierHelper = new DefaultEventHandlerIdentifierHelper($typeFormatter, [
            'Class\\Path\\One' => [
                'handler.one',
                'handler.two',
            ],
            'Class\\Path\\Two' => [
                'handler.three',
                123,
            ],
        ]);

        $typeFormatter
            ->expects()
            ->format(123)
            ->andReturn('TYPE');

        $defaultEventHandlerIdentifierHelper->identify('Class\\Path\\Two');
    }
}
