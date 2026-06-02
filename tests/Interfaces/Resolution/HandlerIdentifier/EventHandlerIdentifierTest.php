<?php
declare(strict_types=1);

namespace Tests\Interfaces\Resolution\HandlerIdentifier;

use Pathway\Resolution\HandlerIdentifier\EventHandlerIdentifier;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class EventHandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(EventHandlerIdentifier::class);
    }
}
