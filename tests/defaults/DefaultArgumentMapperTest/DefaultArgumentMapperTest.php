<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultArgumentMapperTest;

use Pathway\Invocation\DefaultArgumentMapperException;
use Pathway\Invocation\ArgumentMapperException;
use Pathway\Invocation\DefaultArgumentMapper;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Info\MethodInfo;
use Pathway\Info\ClassInfo;

use Tests\Defaults\DefaultArgumentMapperTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use stdClass;

/**
 * @phpstan-type ListScenario array{
 *     class: class-string,
 *     value: list<mixed>
 * }
 * @phpstan-type MappingScenario array{
 *     expected: list<mixed>,
 *     class: class-string,
 *     value: array<string, mixed>
 * }
 * @phpstan-type FailureScenario array{
 *     class: class-string,
 *     value: mixed,
 *     expectedMessage: string
 * }
 */
final class DefaultArgumentMapperTest extends TestCase
{
    private readonly DefaultArgumentMapper $defaultArgumentMapper;

    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultArgumentMapper::class);
    }

    #[Test]
    public function it_implements_the_argument_mapper_interface(): void
    {
        $this->assertImplementsInterface(DefaultArgumentMapper::class, ArgumentMapper::class);
    }

    #[Test]
    public function its_exception_exists(): void
    {
        $this->assertClassExists(DefaultArgumentMapperException::class);
    }

    #[Test]
    public function its_exception_implements_the_argument_mapper_exception_interface(): void
    {
        $this->assertImplementsInterface(DefaultArgumentMapperException::class, ArgumentMapperException::class);
    }

    /**
     * @param class-string $class
     * @param list<mixed> $value
     */
    #[Test]
    #[DataProvider('provider_it_returns_a_list_immediately')]
    public function it_returns_a_list_immediately(string $class, array $value): void
    {
        $this->assertSame($value, $this->defaultArgumentMapper->resolve($this->methodInfo($class), $value));
    }

    /**
     * @return array<string, ListScenario>
     */
    public static function provider_it_returns_a_list_immediately(): array
    {
        return [
            'empty list' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => [],
            ],
            'matching list' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['a', 1],
            ],
            'list with more values than parameters' => [
                'class' => Fixtures\NoParameters::class,
                'value' => ['a', 'b', 'c'],
            ],
            'list with nulls' => [
                'class' => Fixtures\NullableParameters::class,
                'value' => [null, null],
            ],
        ];
    }

    /**
     * @param list<mixed> $expected
     * @param class-string $class
     * @param array<string, mixed> $value
     */
    #[Test]
    #[DataProvider('provider_it_maps_an_associative_array_onto_the_parameters')]
    public function it_maps_an_associative_array_onto_the_parameters(array $expected, string $class, array $value): void
    {
        $this->assertSame($expected, $this->defaultArgumentMapper->resolve($this->methodInfo($class), $value));
    }

    /**
     * @return array<string, MappingScenario>
     */
    public static function provider_it_maps_an_associative_array_onto_the_parameters(): array
    {
        return [
            'in parameter order' => [
                'expected' => ['a', 1],
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['first' => 'a', 'second' => 1],
            ],
            'in a different order' => [
                'expected' => ['a', 1],
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['second' => 1, 'first' => 'a'],
            ],
            'explicit nulls are kept' => [
                'expected' => [null, null],
                'class' => Fixtures\NullableParameters::class,
                'value' => ['first' => null, 'second' => null],
            ],
            'omitted trailing defaults are not passed' => [
                'expected' => ['a'],
                'class' => Fixtures\DefaultParameters::class,
                'value' => ['first' => 'a'],
            ],
            'skipped optional parameters are filled with defaults' => [
                'expected' => ['a', 'second', 'c'],
                'class' => Fixtures\DefaultParameters::class,
                'value' => ['first' => 'a', 'third' => 'c'],
            ],
            'supplied optional parameters override defaults' => [
                'expected' => ['a', 'b', 'c'],
                'class' => Fixtures\DefaultParameters::class,
                'value' => ['third' => 'c', 'second' => 'b', 'first' => 'a'],
            ],
            'omitted variadic parameter' => [
                'expected' => ['a'],
                'class' => Fixtures\VariadicParameters::class,
                'value' => ['first' => 'a'],
            ],
            'variadic parameter is spread' => [
                'expected' => ['a', 'b', 'c', 'd'],
                'class' => Fixtures\VariadicParameters::class,
                'value' => ['rest' => ['c', 'd'], 'second' => 'b', 'first' => 'a'],
            ],
            'variadic parameter fills skipped defaults' => [
                'expected' => ['a', 'second', 'c'],
                'class' => Fixtures\VariadicParameters::class,
                'value' => ['first' => 'a', 'rest' => ['c']],
            ],
            'empty variadic parameter' => [
                'expected' => ['a'],
                'class' => Fixtures\VariadicParameters::class,
                'value' => ['first' => 'a', 'rest' => []],
            ],
            'only a variadic parameter' => [
                'expected' => ['x', 'y'],
                'class' => Fixtures\OnlyVariadicParameter::class,
                'value' => ['items' => ['x', 'y']],
            ],
        ];
    }

    /**
     * @param class-string $class
     */
    #[Test]
    #[DataProvider('provider_it_throws_if_the_arguments_cannot_be_mapped')]
    public function it_throws_if_the_arguments_cannot_be_mapped(string $class, mixed $value, string $expectedMessage): void
    {
        $this->expectException(DefaultArgumentMapperException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->defaultArgumentMapper->resolve($this->methodInfo($class), $value);
    }

    /**
     * @return array<string, FailureScenario>
     */
    public static function provider_it_throws_if_the_arguments_cannot_be_mapped(): array
    {
        return [
            'null' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => null,
                'expectedMessage' => 'Arguments for method "' . Fixtures\RequiredParameters::class . '::handle" must be an array, "null" given.',
            ],
            'string' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => 'a',
                'expectedMessage' => 'Arguments for method "' . Fixtures\RequiredParameters::class . '::handle" must be an array, "string" given.',
            ],
            'object' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => new stdClass(),
                'expectedMessage' => 'Arguments for method "' . Fixtures\RequiredParameters::class . '::handle" must be an array, "object(stdClass)" given.',
            ],
            'missing required argument' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['first' => 'a'],
                'expectedMessage' => 'Missing required argument "second" for method "' . Fixtures\RequiredParameters::class . '::handle".',
            ],
            'missing required argument before a supplied optional one' => [
                'class' => Fixtures\DefaultParameters::class,
                'value' => ['second' => 'b'],
                'expectedMessage' => 'Missing required argument "first" for method "' . Fixtures\DefaultParameters::class . '::handle".',
            ],
            'nullable parameter without a default is still required' => [
                'class' => Fixtures\NullableParameters::class,
                'value' => ['second' => null],
                'expectedMessage' => 'Missing required argument "first" for method "' . Fixtures\NullableParameters::class . '::handle".',
            ],
            'unknown key' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['first' => 'a', 'second' => 1, 'third' => 2],
                'expectedMessage' => 'Method "' . Fixtures\RequiredParameters::class . '::handle" does not have a parameter named "third".',
            ],
            'multiple unknown keys' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['first' => 'a', 'second' => 1, 'third' => 2, 'fourth' => 3],
                'expectedMessage' => 'Method "' . Fixtures\RequiredParameters::class . '::handle" does not have parameters named "third", "fourth".',
            ],
            'unknown keys are reported before missing arguments' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['nope' => 'a'],
                'expectedMessage' => 'Method "' . Fixtures\RequiredParameters::class . '::handle" does not have a parameter named "nope".',
            ],
            'list index mixed with names' => [
                'class' => Fixtures\RequiredParameters::class,
                'value' => ['a', 'second' => 1],
                'expectedMessage' => 'Method "' . Fixtures\RequiredParameters::class . '::handle" does not have a parameter named "0".',
            ],
            'associative array for a method without parameters' => [
                'class' => Fixtures\NoParameters::class,
                'value' => ['first' => 'a'],
                'expectedMessage' => 'Method "' . Fixtures\NoParameters::class . '::handle" does not have a parameter named "first".',
            ],
            'variadic argument is not an array' => [
                'class' => Fixtures\OnlyVariadicParameter::class,
                'value' => ['items' => 'x'],
                'expectedMessage' => 'Argument "items" for variadic parameter of method "' . Fixtures\OnlyVariadicParameter::class . '::handle" must be a list, "string" given.',
            ],
            'variadic argument is not a list' => [
                'class' => Fixtures\OnlyVariadicParameter::class,
                'value' => ['items' => ['a' => 'x']],
                'expectedMessage' => 'Argument "items" for variadic parameter of method "' . Fixtures\OnlyVariadicParameter::class . '::handle" must be a list, "array" given.',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultArgumentMapper = new DefaultArgumentMapper();
    }

    /**
     * @param class-string $class
     */
    private function methodInfo(string $class): MethodInfo
    {
        $methodInfo = new ClassInfo($class)->getMethodInfo('handle');

        $this->assertNotNull($methodInfo);

        return $methodInfo;
    }
}
