<?php
declare(strict_types=1);

namespace Tests\Interfaces\Invocation\ArgumentResolver;

use Pathway\Invocation\ArgumentResolver\ArgumentResolverException;
use Pathway\Invocation\InvocationException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ArgumentResolverExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(ArgumentResolverException::class);
    }

    #[Test]
    public function it_implements_the_invocation_exception_interface(): void
    {
        $this->assertChildOf(ArgumentResolverException::class, InvocationException::class);
    }
}
