<?php

declare(strict_types=1);

namespace Tests\Unclassified;

use Pathway\Resolution\DefaultHandlerIdentifier;
use Pathway\Resolution\HandlerIdentifier;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DefaultHandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultHandlerIdentifier::class);
    }

    #[Test]
    public function it_implements_the_command_handler_indentifier_interface(): void
    {
        $this->assertImplementsInterface(DefaultHandlerIdentifier::class, HandlerIdentifier::class);
    }

    #[Test]
    public function it_appends_handler_to_the_command_class(): void
    {
        $defaultCommandHandlerIdentifier = new DefaultHandlerIdentifier();

        $result = $defaultCommandHandlerIdentifier->identify('Some\\Command');

        $this->assertSame('Some\\CommandHandler', $result);
    }
}
