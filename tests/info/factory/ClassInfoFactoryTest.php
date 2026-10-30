<?php

declare(strict_types=1);

namespace Tests\Info\Factory;

use Pathway\Info\ClassInfoFactory;

use Tests\Info\Factory\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

final class ClassInfoFactoryTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ClassInfoFactory::class);
    }

    #[Test]
    public function it_makes_a_class_info_for_the_given_class(): void
    {
        $result = ClassInfoFactory::make(Fixtures\ClassA::class);

        $this->assertNotNull($result);

        $this->assertSame(Fixtures\ClassA::class, $result->getName());
    }

    #[Test]
    public function it_only_makes_a_class_info_once_per_class(): void
    {
        $result1 = ClassInfoFactory::make(Fixtures\ClassA::class);
        $result2 = ClassInfoFactory::make(Fixtures\ClassA::class);

        $this->assertNotNull($result1);
        $this->assertNotNull($result2);

        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function it_makes_a_separate_class_info_for_each_class(): void
    {
        $result1 = ClassInfoFactory::make(Fixtures\ClassA::class);
        $result2 = ClassInfoFactory::make(Fixtures\ClassB::class);

        $this->assertNotNull($result1);
        $this->assertNotNull($result2);

        $this->assertNotSame($result1, $result2);

        $this->assertSame(Fixtures\ClassB::class, $result2->getName());
    }

    #[Test]
    public function it_returns_null_if_class_does_not_exist(): void
    {
        $result = ClassInfoFactory::make('_NotAClass');

        $this->assertNull($result);
    }
}
