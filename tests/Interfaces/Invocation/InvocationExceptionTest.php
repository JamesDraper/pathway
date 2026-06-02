<?php
declare(strict_types=1);

namespace Tests\Interfaces\Invocation;

use Pathway\Invocation\InvocationException;
use Pathway\PathwayException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class InvocationExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(InvocationException::class);
    }

    #[Test]
    public function it_implements_the_pathway_exception_interface(): void
    {
        $this->assertChildOf(InvocationException::class, PathwayException::class);
    }
}
