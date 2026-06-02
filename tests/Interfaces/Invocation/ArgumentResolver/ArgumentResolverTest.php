<?php
declare(strict_types=1);

namespace Tests\Interfaces\Invocation\ArgumentResolver;

use Pathway\Invocation\ArgumentResolver\ArgumentResolver;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ArgumentResolverTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(ArgumentResolver::class);
    }
}
