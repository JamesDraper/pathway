<?php
declare(strict_types=1);

namespace Tests\Unit;

use Pathway\Dispatcher;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class DispatcherTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(Dispatcher::class);
    }

    #[Test]
    public function it_is_final(): void
    {
        $this->assertFinal(Dispatcher::class);
    }
}
