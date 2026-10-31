<?php

declare(strict_types=1);

namespace Tests\MethodChain;

use Pathway\Invocation\DefaultArgumentMapperException;
use Pathway\Invocation\ArgumentMismatchException;
use Pathway\Invocation\DefaultArgumentMapper;
use Pathway\Invocation\InvocationException;
use Pathway\HandlerDefinitionException;
use Pathway\Dispatcher;
use Pathway\PathwayException;
use Pathway\MethodChain;

use Tests\MethodChain\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use RuntimeException;

/**
 * @phpstan-type MethodScenario array{
 *     handler: object,
 *     method: string
 * }
 * @phpstan-type ExpectedScenario array{
 *     handler: object,
 *     expected: string
 * }
 * @phpstan-type ParameterScenario array{
 *     handler: object,
 *     parameter: string
 * }
 */
final class MethodChainTest extends TestCase
{
    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(MethodChain::class);
    }

    #[Test]
    public function its_exception_implements_the_invocation_exception_interface(): void
    {
        $this->assertImplementsInterface(ArgumentMismatchException::class, InvocationException::class);
    }

    #[Test]
    public function its_handler_definition_exception_implements_the_pathway_exception_interface(): void
    {
        $this->assertImplementsInterface(HandlerDefinitionException::class, PathwayException::class);
    }

    #[Test]
    public function it_runs_prepare_process_and_finalize_in_order(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\AllStagesHandler();
        $dispatcher = $this->makeDummy(Dispatcher::class);
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $dispatcher, $argumentMapper)->invoke($command);

        $this->assertSame('finalized', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $dispatcher]],
            ['method' => 'process', 'arguments' => ['prepared']],
            ['method' => 'finalize', 'arguments' => ['processed']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
            ['method' => 'finalize', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_starts_the_chain_with_process_when_there_is_no_prepare(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\ProcessFinalizeHandler();
        $dispatcher = $this->makeDummy(Dispatcher::class);
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $dispatcher, $argumentMapper)->invoke($command);

        $this->assertSame('finalized', $result);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$command, $dispatcher]],
            ['method' => 'finalize', 'arguments' => ['processed']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'finalize', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_ends_the_chain_with_process_when_there_is_no_finalize(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\PrepareProcessHandler();
        $dispatcher = $this->makeDummy(Dispatcher::class);
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $dispatcher, $argumentMapper)->invoke($command);

        $this->assertSame('processed', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $dispatcher]],
            ['method' => 'process', 'arguments' => ['prepared']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_only_runs_process_when_there_is_no_prepare_or_finalize(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\ProcessOnlyHandler();
        $dispatcher = $this->makeDummy(Dispatcher::class);
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $dispatcher, $argumentMapper)->invoke($command);

        $this->assertSame('processed', $result);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$command, $dispatcher]],
        ], $handler->calls);

        $this->assertSame([], $argumentMapper->calls);
    }

    #[Test]
    public function it_can_be_invoked_more_than_once(): void
    {
        $handler = new Fixtures\ProcessOnlyHandler();
        $dispatcher = $this->makeDummy(Dispatcher::class);
        $methodChain = new MethodChain($handler, $dispatcher, $this->makeArgumentMapper());

        $first = new Fixtures\Command('first');
        $second = new Fixtures\Command('second');

        $methodChain->invoke($first);
        $methodChain->invoke($second);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$first, $dispatcher]],
            ['method' => 'process', 'arguments' => [$second, $dispatcher]],
        ], $handler->calls);
    }

    #[Test]
    public function it_maps_results_with_the_argument_mapper(): void
    {
        $methodChain = new MethodChain(
            new Fixtures\NamedArgumentsHandler(),
            $this->makeDummy(Dispatcher::class),
            new DefaultArgumentMapper(),
        );

        $result = $methodChain->invoke(new Fixtures\Command('hello'));

        $this->assertSame('finalized hello!', $result);
    }

    #[Test]
    public function it_throws_if_the_handler_does_not_have_a_process_method(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Handler class "' . Fixtures\NoProcessHandler::class . '" does not have a "process" method.',
        );

        new MethodChain(new Fixtures\NoProcessHandler(), $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());
    }

    #[Test]
    #[DataProvider('provider_it_throws_if_a_method_is_not_public')]
    public function it_throws_if_a_method_is_not_public(object $handler, string $method): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage('Method "' . $handler::class . '::' . $method . '" must be public.');

        new MethodChain($handler, $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());
    }

    /**
     * @return array<string, MethodScenario>
     */
    public static function provider_it_throws_if_a_method_is_not_public(): array
    {
        return [
            'protected prepare' => [
                'handler' => new Fixtures\ProtectedPrepareHandler(),
                'method' => 'prepare',
            ],
            'private prepare' => [
                'handler' => new Fixtures\PrivatePrepareHandler(),
                'method' => 'prepare',
            ],
            'protected process' => [
                'handler' => new Fixtures\ProtectedProcessHandler(),
                'method' => 'process',
            ],
            'private process' => [
                'handler' => new Fixtures\PrivateProcessHandler(),
                'method' => 'process',
            ],
            'protected finalize' => [
                'handler' => new Fixtures\ProtectedFinalizeHandler(),
                'method' => 'finalize',
            ],
            'private finalize' => [
                'handler' => new Fixtures\PrivateFinalizeHandler(),
                'method' => 'finalize',
            ],
        ];
    }

    #[Test]
    public function it_throws_if_a_method_is_static(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage('Method "' . Fixtures\StaticPrepareHandler::class . '::prepare" must not be static.');

        new MethodChain(new Fixtures\StaticPrepareHandler(), $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());
    }

    #[Test]
    #[DataProvider('it_accepts_a_start_method_with_valid_parameters_provider')]
    public function it_accepts_a_start_method_with_valid_parameters(object $handler, string $expected): void
    {
        $result = new MethodChain($handler, $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper())
            ->invoke(new Fixtures\Command('hello'));

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, ExpectedScenario>
     */
    public static function it_accepts_a_start_method_with_valid_parameters_provider(): array
    {
        return [
            'command only' => [
                'handler' => new Fixtures\CommandOnlyHandler(),
                'expected' => 'hello',
            ],
            'parent types' => [
                'handler' => new Fixtures\ParentTypesHandler(),
                'expected' => 'processed',
            ],
            'optional extra parameters' => [
                'handler' => new Fixtures\OptionalExtraParametersHandler(),
                'expected' => 'hello!',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_throws_if_the_start_method_has_no_parameters_provider')]
    public function it_throws_if_the_start_method_has_no_parameters(object $handler, string $method): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Method "' . $handler::class . '::' . $method . '" must accept the command as its first parameter.',
        );

        new MethodChain($handler, $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());
    }

    /**
     * @return array<string, MethodScenario>
     */
    public static function it_throws_if_the_start_method_has_no_parameters_provider(): array
    {
        return [
            'process' => [
                'handler' => new Fixtures\NoParametersHandler(),
                'method' => 'process',
            ],
            'prepare' => [
                'handler' => new Fixtures\NoParametersPrepareHandler(),
                'method' => 'prepare',
            ],
        ];
    }

    #[Test]
    #[DataProvider('it_throws_if_the_start_method_does_not_accept_the_command_provider')]
    public function it_throws_if_the_start_method_does_not_accept_the_command(object $handler, string $method): void
    {
        $methodChain = new MethodChain($handler, $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());

        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Parameter #1 ($command) of method "' . $handler::class . '::' . $method . '" must accept the command "'
            . Fixtures\Command::class . '", but is of type ' . Fixtures\OtherCommand::class . '.',
        );

        $methodChain->invoke(new Fixtures\Command());
    }

    /**
     * @return array<string, MethodScenario>
     */
    public static function it_throws_if_the_start_method_does_not_accept_the_command_provider(): array
    {
        return [
            'process' => [
                'handler' => new Fixtures\WrongCommandHandler(),
                'method' => 'process',
            ],
            'prepare' => [
                'handler' => new Fixtures\WrongCommandPrepareHandler(),
                'method' => 'prepare',
            ],
        ];
    }

    #[Test]
    public function it_checks_each_command_class_against_the_start_method(): void
    {
        $methodChain = new MethodChain(
            new Fixtures\CommandOnlyHandler(),
            $this->makeDummy(Dispatcher::class),
            $this->makeArgumentMapper(),
        );

        $methodChain->invoke(new Fixtures\Command());

        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Parameter #1 ($command) of method "' . Fixtures\CommandOnlyHandler::class . '::process" must accept the '
            . 'command "' . Fixtures\OtherCommand::class . '", but is of type ' . Fixtures\Command::class . '.',
        );

        $methodChain->invoke(new Fixtures\OtherCommand());
    }

    #[Test]
    #[DataProvider('it_throws_if_the_start_method_does_not_accept_the_dispatcher_provider')]
    public function it_throws_if_the_start_method_does_not_accept_the_dispatcher(
        object $handler,
        string $parameter,
    ): void {
        $dispatcher = $this->makeDummy(Dispatcher::class);

        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Parameter #2 ($' . $parameter . ') of method "' . $handler::class . '::process" must accept the '
            . 'dispatcher "' . $dispatcher::class . '", but is of type ' . Fixtures\Command::class . '.',
        );

        new MethodChain($handler, $dispatcher, $this->makeArgumentMapper());
    }

    /**
     * @return array<string, ParameterScenario>
     */
    public static function it_throws_if_the_start_method_does_not_accept_the_dispatcher_provider(): array
    {
        return [
            'second parameter' => [
                'handler' => new Fixtures\WrongDispatcherHandler(),
                'parameter' => 'dispatcher',
            ],
            'variadic first parameter' => [
                'handler' => new Fixtures\WrongVariadicHandler(),
                'parameter' => 'arguments',
            ],
        ];
    }

    #[Test]
    public function it_throws_if_the_start_method_has_more_than_two_required_parameters(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Method "' . Fixtures\TooManyParametersHandler::class . '::process" must not have more than two required '
            . 'parameters: the command and the dispatcher.',
        );

        new MethodChain(new Fixtures\TooManyParametersHandler(), $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper());
    }

    #[Test]
    public function it_throws_an_invocation_exception_if_a_method_is_given_mismatched_arguments(): void
    {
        $this->expectException(InvocationException::class);
        $this->expectExceptionMessage(
            'Argument #1 ($value) for method "' . Fixtures\MismatchedProcessHandler::class . '::process" must be of type int, string given.',
        );

        new MethodChain(new Fixtures\MismatchedProcessHandler(), $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_handler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong.');

        new MethodChain(new Fixtures\ThrowingHandler(), $this->makeDummy(Dispatcher::class), $this->makeArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_argument_mapper(): void
    {
        $this->expectException(DefaultArgumentMapperException::class);

        new MethodChain(new Fixtures\AllStagesHandler(), $this->makeDummy(Dispatcher::class), new DefaultArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    private function makeArgumentMapper(): Fixtures\RecordingArgumentMapper
    {
        return new Fixtures\RecordingArgumentMapper();
    }
}
