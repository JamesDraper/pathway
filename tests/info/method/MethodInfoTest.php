<?php

declare(strict_types=1);

namespace Tests\Info\Method;

use Pathway\Info\ParameterInfo;
use Pathway\Info\MethodInfo;
use Pathway\Info\ClassInfo;

use Tests\Info\Method\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionMethod;

/**
 * @phpstan-type MethodInfoScenario array{
 *     expectedIsPublic: bool,
 *     expectedIsProtected: bool,
 *     expectedIsPrivate: bool,
 *     expectedIsStatic: bool,
 *     class: class-string,
 *     method: string
 * }
 */
final class MethodInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(MethodInfo::class);
    }

    /**
     * @param class-string $class
     */
    #[Test]
    #[DataProvider('provider_it_gets_method_info')]
    public function it_gets_method_info(
        bool $expectedIsPublic,
        bool $expectedIsProtected,
        bool $expectedIsPrivate,
        bool $expectedIsStatic,
        string $class,
        string $method,
    ): void {
        $methodInfo = new MethodInfo(new ReflectionMethod($class, $method), $this->makeDummy(ClassInfo::class));

        $this->assertSame($expectedIsPublic, $methodInfo->isPublic());
        $this->assertSame($expectedIsProtected, $methodInfo->isProtected());
        $this->assertSame($expectedIsPrivate, $methodInfo->isPrivate());
        $this->assertSame($expectedIsStatic, $methodInfo->isStatic());
        $this->assertSame($method, $methodInfo->getName());
    }

    /**
     * @return array<string, MethodInfoScenario>
     */
    public static function provider_it_gets_method_info(): array
    {
        return [
            'public method' => [
                'expectedIsPublic' => true,
                'expectedIsProtected' => false,
                'expectedIsPrivate' => false,
                'expectedIsStatic' => false,
                'class' => Fixtures\PublicMethod::class,
                'method' => 'greetPublic',
            ],
            'protected method' => [
                'expectedIsPublic' => false,
                'expectedIsProtected' => true,
                'expectedIsPrivate' => false,
                'expectedIsStatic' => false,
                'class' => Fixtures\ProtectedMethod::class,
                'method' => 'greetProtected',
            ],
            'private method' => [
                'expectedIsPublic' => false,
                'expectedIsProtected' => false,
                'expectedIsPrivate' => true,
                'expectedIsStatic' => false,
                'class' => Fixtures\PrivateMethod::class,
                'method' => 'greetPrivate',
            ],
            'no visibility method' => [
                'expectedIsPublic' => true,
                'expectedIsProtected' => false,
                'expectedIsPrivate' => false,
                'expectedIsStatic' => false,
                'class' => Fixtures\NoVisibilityMethod::class,
                'method' => 'greetNoVisibility',
            ],
            'static' => [
                'expectedIsPublic' => true,
                'expectedIsProtected' => false,
                'expectedIsPrivate' => false,
                'expectedIsStatic' => true,
                'class' => Fixtures\StaticMethod::class,
                'method' => 'greetPublic',
            ],
        ];
    }

    #[Test]
    public function getParameterInfos_builds_parameter_infos_from_the_method_parameters(): void
    {
        $methodInfo = new MethodInfo(
            new ReflectionMethod(Fixtures\WithParametersMethod::class, 'greet'),
            $this->makeDummy(ClassInfo::class),
        );

        $parameterInfos = $methodInfo->getParameterInfos();

        $this->assertCount(2, $parameterInfos);
        $this->assertContainsOnlyInstancesOf(ParameterInfo::class, $parameterInfos);
        $this->assertSame('greeting', $parameterInfos[0]->getName());
        $this->assertFalse($parameterInfos[0]->isVariadic());
        $this->assertSame('names', $parameterInfos[1]->getName());
        $this->assertTrue($parameterInfos[1]->isVariadic());
    }

    #[Test]
    public function getParameterInfos_returns_an_empty_list_when_there_are_no_parameters(): void
    {
        $methodInfo = new MethodInfo(
            new ReflectionMethod(Fixtures\PublicMethod::class, 'greetPublic'),
            $this->makeDummy(ClassInfo::class),
        );

        $this->assertSame([], $methodInfo->getParameterInfos());
    }

    #[Test]
    public function getParameterInfos_caches_the_parameter_infos_after_first_generation(): void
    {
        $methodInfo = new MethodInfo(
            new ReflectionMethod(Fixtures\WithParametersMethod::class, 'greet'),
            $this->makeDummy(ClassInfo::class),
        );

        $first = $methodInfo->getParameterInfos();
        $second = $methodInfo->getParameterInfos();

        $this->assertSame($first, $second);
        $this->assertSame($first[0], $second[0]);
        $this->assertSame($first[1], $second[1]);
    }

    #[Test]
    public function getClassInfo_returns_the_class_info_given_to_the_constructor(): void
    {
        $classInfo = $this->makeDummy(ClassInfo::class);

        $methodInfo = new MethodInfo(new ReflectionMethod(Fixtures\PublicMethod::class, 'greetPublic'), $classInfo);

        $this->assertSame($classInfo, $methodInfo->getClassInfo());
    }

    #[Test]
    public function getParameterInfos_passes_itself_to_the_parameter_infos(): void
    {
        $methodInfo = new MethodInfo(
            new ReflectionMethod(Fixtures\WithParametersMethod::class, 'greet'),
            $this->makeDummy(ClassInfo::class),
        );

        foreach ($methodInfo->getParameterInfos() as $parameterInfo) {
            $this->assertSame($methodInfo, $parameterInfo->getMethodInfo());
        }
    }
}
