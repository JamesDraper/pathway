<?php

declare(strict_types=1);

namespace Tests\Interfaces\Resolution;

use Pathway\Resolution\ResolutionException;
use Pathway\PathwayException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ResolutionExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(ResolutionException::class);
    }

    #[Test]
    public function it_extends_the_pathway_exception_interface(): void
    {
        $this->assertExtendsInterface(ResolutionException::class, PathwayException::class);
    }
}
