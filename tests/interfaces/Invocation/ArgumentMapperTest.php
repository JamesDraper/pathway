<?php

declare(strict_types=1);

namespace Tests\Interfaces\Invocation;

use Pathway\Invocation\ArgumentMapper;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ArgumentMapperTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertInterfaceExists(ArgumentMapper::class);
    }
}
