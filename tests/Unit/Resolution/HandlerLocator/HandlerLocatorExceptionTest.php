<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerLocator;

use Pathway\Resolution\HandlerLocator\HandlerLocatorException;
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
    public function it_implements_the_resolution_exception_interface(): void
    {
        $this->assertChildOf(HandlerLocatorException::class, ResolutionException::class);
    }
}
