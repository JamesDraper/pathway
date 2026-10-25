<?php

declare(strict_types=1);

namespace Tests\Interfaces;

use Pathway\PathwayException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Throwable;

final class PathwayExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(PathwayException::class);
    }

    #[Test]
    public function it_extends_the_throwable_interface(): void
    {
        $this->assertExtendsInterface(PathwayException::class, Throwable::class);
    }
}
