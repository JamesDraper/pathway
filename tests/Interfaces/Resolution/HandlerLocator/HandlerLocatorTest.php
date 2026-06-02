<?php
declare(strict_types=1);

namespace Tests\Interfaces\Resolution\HandlerLocator;

use Pathway\Resolution\HandlerLocator\HandlerLocator;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerLocatorTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(HandlerLocator::class);
    }
}
