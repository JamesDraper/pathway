<?php

declare(strict_types=1);

namespace Tests\Method;

use Pathway\Invocation\DefaultArgumentMapperException;
use Pathway\Invocation\ArgumentMismatchException;
use Pathway\Invocation\DefaultArgumentMapper;
use Pathway\Invocation\InvocationException;
use Pathway\Info\MethodInfo;
use Pathway\Info\ClassInfo;
use Pathway\Method;

use Tests\Method\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use ArgumentCountError;
use RuntimeException;
use Throwable;
use TypeError;
use stdClass;

/**
 * @phpstan-type ResultScenario array{
 *     class: class-string,
 *     value: list<mixed>,
 *     expected: mixed
 * }
 * @phpstan-type MismatchScenario array{
 *     class: class-string,
 *     value: list<mixed>,
 *     expectedMessage: string,
 *     expectedPrevious: class-string<Throwable>
 * }
 * @phpstan-type RethrowScenario array{
 *     class: class-string,
 *     expectedClass: class-string<Throwable>,
 *     expectedMessage: string
 * }
 */
final class MethodTest extends TestCase
{
    private const string METHOD = 'run';

    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(Method::class);
    }

    #[Test]
    public function its_exception_implements_the_invocation_exception_interface(): void
    {
        $this->assertImplementsInterface(ArgumentMismatchException::class, InvocationException::class);
    }

    /**
     * @param class-string $class
     * @param list<mixed> $value
     */
    #[Test]
    #[DataProvider('provider_it_returns_the_result_of_the_method')]
    public function it_returns_the_result_of_the_method(string $class, array $value, mixed $expected): void
    {
        $result = $this->makeMethod($class)->invoke(new $class(), $value);

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, ResultScenario>
     */
    public static function provider_it_returns_the_result_of_the_method(): array
    {
        return [
            'typed' => [
                'class' => Fixtures\TypedMethod::class,
                'value' => [1, 'a'],
                'expected' => '1a',
            ],
            'optional omitted' => [
                'class' => Fixtures\OptionalMethod::class,
                'value' => [1],
                'expected' => 3,
            ],
            'optional given' => [
                'class' => Fixtures\OptionalMethod::class,
                'value' => [1, 5],
                'expected' => 6,
            ],
            'variadic' => [
                'class' => Fixtures\VariadicMethod::class,
                'value' => ['label', 1, 2, 3],
                'expected' => [1, 2, 3],
            ],
            'extra arguments are ignored' => [
                'class' => Fixtures\UntypedMethod::class,
                'value' => ['value', 'extra'],
                'expected' => 'value',
            ],
        ];
    }

    #[Test]
    public function it_passes_the_method_info_and_value_to_the_argument_mapper(): void
    {
        $methodInfo = $this->methodInfo(Fixtures\TypedMethod::class);
        $argumentMapper = $this->makeArgumentMapper();

        new Method($methodInfo, $argumentMapper)->invoke(new Fixtures\TypedMethod(), [1, 'a']);

        $this->assertCount(1, $argumentMapper->calls);
        $this->assertSame($methodInfo, $argumentMapper->calls[0]['methodInfo']);
        $this->assertSame([1, 'a'], $argumentMapper->calls[0]['value']);
    }

    #[Test]
    public function it_invokes_the_method_with_the_mapped_arguments(): void
    {
        $method = new Method($this->methodInfo(Fixtures\TypedMethod::class), new DefaultArgumentMapper());

        $this->assertSame('1a', $method->invoke(new Fixtures\TypedMethod(), ['text' => 'a', 'number' => 1]));
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_argument_mapper(): void
    {
        $method = new Method($this->methodInfo(Fixtures\TypedMethod::class), new DefaultArgumentMapper());

        $this->expectException(DefaultArgumentMapperException::class);

        $method->invoke(new Fixtures\TypedMethod(), 'not an array');
    }

    /**
     * @param class-string $class
     * @param list<mixed> $value
     * @param class-string<Throwable> $expectedPrevious
     */
    #[Test]
    #[DataProvider('provider_it_throws_if_the_arguments_do_not_match_the_parameters')]
    public function it_throws_if_the_arguments_do_not_match_the_parameters(
        string $class,
        array $value,
        string $expectedMessage,
        string $expectedPrevious,
    ): void {
        try {
            $this->makeMethod($class)->invoke(new $class(), $value);
        } catch (ArgumentMismatchException $exception) {
            $this->assertSame($expectedMessage, $exception->getMessage());
            $this->assertInstanceOf($expectedPrevious, $exception->getPrevious());

            return;
        }

        $this->fail('Expected an ArgumentMismatchException to be thrown.');
    }

    /**
     * @return array<string, MismatchScenario>
     */
    public static function provider_it_throws_if_the_arguments_do_not_match_the_parameters(): array
    {
        $typed = Fixtures\TypedMethod::class . '::' . self::METHOD;
        $optional = Fixtures\OptionalMethod::class . '::' . self::METHOD;
        $variadic = Fixtures\VariadicMethod::class . '::' . self::METHOD;

        return [
            'no arguments' => [
                'class' => Fixtures\TypedMethod::class,
                'value' => [],
                'expectedMessage' => 'Too few arguments for method "' . $typed . '": 2 required, 0 given.',
                'expectedPrevious' => ArgumentCountError::class,
            ],
            'too few arguments' => [
                'class' => Fixtures\TypedMethod::class,
                'value' => [1],
                'expectedMessage' => 'Too few arguments for method "' . $typed . '": 2 required, 1 given.',
                'expectedPrevious' => ArgumentCountError::class,
            ],
            'first argument wrong type' => [
                'class' => Fixtures\TypedMethod::class,
                'value' => ['1', 'a'],
                'expectedMessage' => 'Argument #1 ($number) for method "' . $typed . '" must be of type int, string given.',
                'expectedPrevious' => TypeError::class,
            ],
            'second argument wrong type' => [
                'class' => Fixtures\TypedMethod::class,
                'value' => [1, new stdClass()],
                'expectedMessage' => 'Argument #2 ($text) for method "' . $typed . '" must be of type string, object(stdClass) given.',
                'expectedPrevious' => TypeError::class,
            ],
            'optional argument wrong type' => [
                'class' => Fixtures\OptionalMethod::class,
                'value' => [1, null],
                'expectedMessage' => 'Argument #2 ($second) for method "' . $optional . '" must be of type int, null given.',
                'expectedPrevious' => TypeError::class,
            ],
            'variadic argument wrong type' => [
                'class' => Fixtures\VariadicMethod::class,
                'value' => ['label', 1, 'two'],
                'expectedMessage' => 'Argument #3 ($numbers) for method "' . $variadic . '" must be of type int, string given.',
                'expectedPrevious' => TypeError::class,
            ],
        ];
    }

    /**
     * @param class-string $class
     * @param class-string<Throwable> $expectedClass
     */
    #[Test]
    #[DataProvider('provider_it_rethrows_exceptions_thrown_by_the_method')]
    public function it_rethrows_exceptions_thrown_by_the_method(string $class, string $expectedClass, string $expectedMessage): void
    {
        $this->expectException($expectedClass);
        $this->expectExceptionMessage($expectedMessage);

        $this->makeMethod($class)->invoke(new $class(), [1]);
    }

    /**
     * @return array<string, RethrowScenario>
     */
    public static function provider_it_rethrows_exceptions_thrown_by_the_method(): array
    {
        return [
            'runtime exception' => [
                'class' => Fixtures\RuntimeExceptionMethod::class,
                'expectedClass' => RuntimeException::class,
                'expectedMessage' => 'Something went wrong.',
            ],
            'type error from inside the method' => [
                'class' => Fixtures\TypeErrorMethod::class,
                'expectedClass' => TypeError::class,
                'expectedMessage' => 'Something inside went wrong.',
            ],
        ];
    }

    /**
     * @param class-string $class
     */
    private function makeMethod(string $class): Method
    {
        return new Method($this->methodInfo($class), $this->makeArgumentMapper());
    }

    private function makeArgumentMapper(): Fixtures\RecordingArgumentMapper
    {
        return new Fixtures\RecordingArgumentMapper();
    }

    /**
     * @param class-string $class
     */
    private function methodInfo(string $class): MethodInfo
    {
        $methodInfo = new ClassInfo($class)->getMethodInfo(self::METHOD);

        $this->assertNotNull($methodInfo);

        return $methodInfo;
    }
}
