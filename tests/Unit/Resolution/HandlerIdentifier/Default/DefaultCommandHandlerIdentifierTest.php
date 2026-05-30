<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\Default\DefaultCommandHandlerIdentifier;
use Pathway\Resolution\HandlerIdentifier\CommandHandlerIdentifier;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DefaultCommandHandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultCommandHandlerIdentifier::class);
    }

    #[Test]
    public function it_is_final(): void
    {
        $this->assertFinal(DefaultCommandHandlerIdentifier::class);
    }

    #[Test]
    public function it_implements_the_command_handler_indentifier_interface(): void
    {
        $this->assertChildOf(DefaultCommandHandlerIdentifier::class, CommandHandlerIdentifier::class);
    }

    #[Test]
    public function it_appends_handler_to_the_command_class(): void
    {
        $defaultCommandHandlerIdentifier = new DefaultCommandHandlerIdentifier();

        $result = $defaultCommandHandlerIdentifier->identify('Some\\Command');

        $this->assertSame('Some\\CommandHandler', $result);
    }
}
