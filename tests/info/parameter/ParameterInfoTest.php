<?php

declare(strict_types=1);

namespace Tests\Info\Parameter;

use Pathway\Info\ParameterInfo;
use Pathway\Info\MethodInfo;
use Pathway\Info\TypeInfo;

use Tests\Info\Parameter\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ReflectionParameter;
use ReflectionClass;

use function array_shift;

final class ParameterInfoTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(ParameterInfo::class);
    }

    #[Test]
    #[DataProvider('provider_it_gets_parameter_info')]
    public function it_gets_parameter_info(
        string $expectedName,
        bool $expectedIsVariadric,
        bool $expectedHasDefault,
        mixed $expectedDefault,
        string $class,
        string $method,
    ): void {
        $reflectionParameter = $this->getReflectionParameter($class, $method);

        $parameterInfo = new ParameterInfo($reflectionParameter, $this->makeDummy(MethodInfo::class));

        $this->assertSame($expectedName, $parameterInfo->getName());
        $this->assertSame($expectedIsVariadric, $parameterInfo->isVariadic());
        $this->assertSame($expectedHasDefault, $parameterInfo->hasDefault());
        $this->assertSame($expectedDefault, $parameterInfo->getDefault());
        $this->assertInstanceOf(TypeInfo::class, $parameterInfo->getTypeInfo());
    }

    public static function provider_it_gets_parameter_info(): array
    {
        return [
            [
                'expectedName' => 'str',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'class' => Fixtures\SimpleParameter::class,
                'method' => 'reverse',
            ],
            [
                'expectedName' => 'strs',
                'expectedIsVariadric' => true,
                'expectedHasDefault' => false,
                'expectedDefault' => null,
                'class' => Fixtures\VariadicParameter::class,
                'method' => 'concatenate',
            ],
            [
                'expectedName' => 'user',
                'expectedIsVariadric' => false,
                'expectedHasDefault' => true,
                'expectedDefault' => 'guest',
                'class' => Fixtures\DefaultValueParameter::class,
                'method' => 'greet',
            ],
        ];
    }

    #[Test]
    public function getTypeInfo_builds_type_info_from_the_parameter_type(): void
    {
        $reflectionParameter = $this->getReflectionParameter(Fixtures\SimpleParameter::class, 'reverse');

        $parameterInfo = new ParameterInfo($reflectionParameter, $this->makeDummy(MethodInfo::class));

        $typeInfo = $parameterInfo->getTypeInfo();

        $this->assertSame('string', $typeInfo->toString());
    }

    #[Test]
    public function getTypeInfo_caches_the_type_info_after_first_generation(): void
    {
        $reflectionParameter = $this->getReflectionParameter(Fixtures\SimpleParameter::class, 'reverse');

        $parameterInfo = new ParameterInfo($reflectionParameter, $this->makeDummy(MethodInfo::class));

        $this->assertSame($parameterInfo->getTypeInfo(), $parameterInfo->getTypeInfo());
    }

    #[Test]
    public function getMethodInfo_returns_the_method_info_given_to_the_constructor(): void
    {
        $reflectionParameter = $this->getReflectionParameter(Fixtures\SimpleParameter::class, 'reverse');
        $methodInfo = $this->makeDummy(MethodInfo::class);

        $parameterInfo = new ParameterInfo($reflectionParameter, $methodInfo);

        $this->assertSame($methodInfo, $parameterInfo->getMethodInfo());
    }

    #[Test]
    public function getTypeInfo_passes_itself_to_the_type_info(): void
    {
        $reflectionParameter = $this->getReflectionParameter(Fixtures\SimpleParameter::class, 'reverse');

        $parameterInfo = new ParameterInfo($reflectionParameter, $this->makeDummy(MethodInfo::class));

        $this->assertSame($parameterInfo, $parameterInfo->getTypeInfo()->getParameterInfo());
    }

    private function getReflectionParameter(string $class, string $method): ReflectionParameter
    {
        $reflectionClass = new ReflectionClass($class);

        $reflectionParameters = $reflectionClass->getMethod($method)->getParameters();

        return array_shift($reflectionParameters);
    }
}
