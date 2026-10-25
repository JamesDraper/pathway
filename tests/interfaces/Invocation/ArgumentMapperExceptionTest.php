<?php

declare(strict_types=1);

namespace Tests\Interfaces\Invocation;

use Pathway\Invocation\ArgumentMapperException;
use Pathway\Invocation\InvocationException;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ArgumentMapperExceptionTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(ArgumentMapperException::class);
    }

    #[Test]
    public function it_extends_the_invocation_exception_interface(): void
    {
        $this->assertExtendsInterface(ArgumentMapperException::class, InvocationException::class);
    }
}
