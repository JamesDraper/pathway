<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerIdentifier\Default;

use Pathway\Resolution\HandlerIdentifier\Default\DefaultEventHandlerIdentifierException;
use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifierException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DefaultEventHandlerIdentifierExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultEventHandlerIdentifierException::class);
    }

    #[Test]
    public function it_is_final(): void
    {
        $this->assertFinal(DefaultEventHandlerIdentifierException::class);
    }

    #[Test]
    public function it_implements_the_event_handler_indentifier_exception(): void
    {
        $this->assertChildOf(DefaultEventHandlerIdentifierException::class, EventHandlerIdentifierException::class);
    }
}
