<?php

declare(strict_types=1);

namespace Tests\Interfaces\Resolution;

use Pathway\Resolution\HandlerLocatorException;
use Pathway\Resolution\ResolutionException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerLocatorExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(HandlerLocatorException::class);
    }

    #[Test]
    public function it_extends_the_resolution_exception_interface(): void
    {
        $this->assertExtendsInterface(HandlerLocatorException::class, ResolutionException::class);
    }
}
