<?php
declare(strict_types=1);

namespace Tests\Unit\Resolution\HandlerIdentifier;

use Pathway\Resolution\HandlerIdentifier\HandlerIdentifierException;
use Pathway\Resolution\ResolutionException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class HandlerIdentifierExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(HandlerIdentifierException::class);
    }

    #[Test]
    public function it_implements_the_resolution_exception_interface(): void
    {
        $this->assertChildOf(HandlerIdentifierException::class, ResolutionException::class);
    }
}
