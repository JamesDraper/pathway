<?php
declare(strict_types=1);

namespace Tests\Interfaces;

use Pathway\DispatcherInterface;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DispatcherInterfaceTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(DispatcherInterface::class);
    }
}
