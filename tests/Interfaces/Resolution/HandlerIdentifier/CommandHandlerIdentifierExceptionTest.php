<?php
declare(strict_types=1);

namespace Tests\Interfaces\Resolution\HandlerIdentifier;

use Pathway\Resolution\HandlerIdentifier\CommandHandlerIdentifierException;
use Pathway\Resolution\HandlerIdentifier\HandlerIdentifierException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class CommandHandlerIdentifierExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(CommandHandlerIdentifierException::class);
    }

    #[Test]
    public function it_implements_the_handler_identifier_exception_interface(): void
    {
        $this->assertChildOf(CommandHandlerIdentifierException::class, HandlerIdentifierException::class);
    }
}
