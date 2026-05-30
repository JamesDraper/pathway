<?php
declare(strict_types=1);

namespace Tests\Unit\Internal\Info;

use Pathway\Internal\Info\ClassInfo;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use ReflectionClass;

final class ClassInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ClassInfo::class);
    }

    #[Test]
    public function it_gets_the_class_name(): void
    {
        $classInfo = new ClassInfo(
            new ReflectionClass(self::class),
        );

        $this->assertSame(
            'Tests\\Unit\\Internal\\Info\\ClassInfoTest',
            $classInfo->getName(),
        );
    }
}
