<?php
declare(strict_types=1);

namespace Tests\Integration\Internal\Info;

use Pathway\Internal\Info\ClassInfoFactory;

use Tests\Fixtures\Internal\Info\ClassInfoFactoryTest as Fixtures;
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
    public function it_returns_a_class_info_object_for_the_class_path(): void
    {
        $classInfoFactory = new ClassInfoFactory();

        $result = $classInfoFactory->make(Fixtures\EmptyClass::class);

        $this->assertNotNull($result);
    }

    #[Test]
    public function it_returns_null_if_class_does_not_exist(): void
    {
        $classInfoFactory = new ClassInfoFactory();

        $result = $classInfoFactory->make(Fixtures\NotAClass::class);

        $this->assertNull($result);
    }
}
