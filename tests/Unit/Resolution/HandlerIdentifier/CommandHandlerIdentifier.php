<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerIdentifier;

use Pathway\Resolution\HandlerIdentifier\CommandHandlerIdentifier;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class CommandHandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(CommandHandlerIdentifier::class);
    }
}
