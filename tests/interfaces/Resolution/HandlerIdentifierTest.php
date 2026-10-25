<?php

declare(strict_types=1);

namespace Tests\Interfaces\Resolution;

use Pathway\Resolution\HandlerIdentifier;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerIdentifierTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(HandlerIdentifier::class);
    }
}
