<?php

declare(strict_types=1);

namespace Tests\MethodChain;

use Pathway\Invocation\DefaultArgumentMapperException;
use Pathway\Invocation\DefaultArgumentMapper;
use Pathway\Invocation\InvocationException;
use Pathway\HandlerDefinitionException;
use Pathway\Invocation\ArgumentMapper;
use Pathway\Internal\MethodChain;
use Pathway\DispatcherException;
use Pathway\PathwayException;
use Pathway\Info\MethodInfo;

use Tests\MethodChain\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use RuntimeException;

use function array_is_list;
use function is_array;

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
        $this->assertImplementsInterface(DispatcherException::class, InvocationException::class);
    }

    #[Test]
    public function its_handler_definition_exception_implements_the_pathway_exception_interface(): void
    {
        $this->assertImplementsInterface(HandlerDefinitionException::class, PathwayException::class);
    }

    #[Test]
    public function it_runs_prepare_process_and_finalise_in_order(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\AllStagesHandler();
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $argumentMapper)->invoke($command);

        $this->assertSame('finalised', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $handler]],
            ['method' => 'process', 'arguments' => ['prepared']],
            ['method' => 'finalise', 'arguments' => ['processed']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
            ['method' => 'finalise', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_starts_the_chain_with_process_when_there_is_no_prepare(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\ProcessFinaliseHandler();
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $argumentMapper)->invoke($command);

        $this->assertSame('finalised', $result);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$command, $handler]],
            ['method' => 'finalise', 'arguments' => ['processed']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'finalise', 'value' => 'processed'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_ends_the_chain_with_process_when_there_is_no_finalise(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\PrepareProcessHandler();
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $argumentMapper)->invoke($command);

        $this->assertSame('processed', $result);

        $this->assertSame([
            ['method' => 'prepare', 'arguments' => [$command, $handler]],
            ['method' => 'process', 'arguments' => ['prepared']],
        ], $handler->calls);

        $this->assertSame([
            ['method' => 'process', 'value' => 'prepared'],
        ], $argumentMapper->calls);
    }

    #[Test]
    public function it_only_runs_process_when_there_is_no_prepare_or_finalise(): void
    {
        $command = new Fixtures\Command();
        $handler = new Fixtures\ProcessOnlyHandler();
        $argumentMapper = $this->makeArgumentMapper();

        $result = new MethodChain($handler, $argumentMapper)->invoke($command);

        $this->assertSame('processed', $result);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$command, $handler]],
        ], $handler->calls);

        $this->assertSame([], $argumentMapper->calls);
    }

    #[Test]
    public function it_can_be_invoked_more_than_once(): void
    {
        $handler = new Fixtures\ProcessOnlyHandler();
        $methodChain = new MethodChain($handler, $this->makeArgumentMapper());

        $first = new Fixtures\Command('first');
        $second = new Fixtures\Command('second');

        $methodChain->invoke($first);
        $methodChain->invoke($second);

        $this->assertSame([
            ['method' => 'process', 'arguments' => [$first, $handler]],
            ['method' => 'process', 'arguments' => [$second, $handler]],
        ], $handler->calls);
    }

    #[Test]
    public function it_maps_results_with_the_argument_mapper(): void
    {
        $result = new MethodChain(new Fixtures\NamedArgumentsHandler(), new DefaultArgumentMapper())
            ->invoke(new Fixtures\Command('hello'));

        $this->assertSame('finalised hello!', $result);
    }

    #[Test]
    public function it_throws_if_the_handler_does_not_have_a_process_method(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage(
            'Handler class "' . Fixtures\NoProcessHandler::class . '" does not have a "process" method.',
        );

        new MethodChain(new Fixtures\NoProcessHandler(), $this->makeArgumentMapper());
    }

    #[Test]
    #[DataProvider('provider_it_throws_if_a_method_is_not_public')]
    public function it_throws_if_a_method_is_not_public(object $handler, string $method): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage('Method "' . $handler::class . '::' . $method . '" must be public.');

        new MethodChain($handler, $this->makeArgumentMapper());
    }

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
            'protected finalise' => [
                'handler' => new Fixtures\ProtectedFinaliseHandler(),
                'method' => 'finalise',
            ],
            'private finalise' => [
                'handler' => new Fixtures\PrivateFinaliseHandler(),
                'method' => 'finalise',
            ],
        ];
    }

    #[Test]
    public function it_throws_if_a_method_is_static(): void
    {
        $this->expectException(HandlerDefinitionException::class);
        $this->expectExceptionMessage('Method "' . Fixtures\StaticPrepareHandler::class . '::prepare" must not be static.');

        new MethodChain(new Fixtures\StaticPrepareHandler(), $this->makeArgumentMapper());
    }

    #[Test]
    public function it_throws_an_invocation_exception_if_a_method_is_given_mismatched_arguments(): void
    {
        $this->expectException(InvocationException::class);
        $this->expectExceptionMessage(
            'Argument #1 ($value) for method "' . Fixtures\MismatchedProcessHandler::class . '::process" must be of type int, string given.',
        );

        new MethodChain(new Fixtures\MismatchedProcessHandler(), $this->makeArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_handler(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Something went wrong.');

        new MethodChain(new Fixtures\ThrowingHandler(), $this->makeArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    #[Test]
    public function it_does_not_wrap_exceptions_thrown_by_the_argument_mapper(): void
    {
        $this->expectException(DefaultArgumentMapperException::class);

        // AllStagesHandler::prepare() returns a string, which the default mapper rejects.
        new MethodChain(new Fixtures\AllStagesHandler(), new DefaultArgumentMapper())
            ->invoke(new Fixtures\Command());
    }

    /**
     * Records each call. A list is returned untouched; anything else is wrapped in a list, so it is passed as the
     * method's only argument.
     */
    private function makeArgumentMapper(): ArgumentMapper
    {
        return new class implements ArgumentMapper {
            /**
             * @var list<array{method: string, value: mixed}>
             */
            public array $calls = [];

            public function resolve(MethodInfo $methodInfo, mixed $value): array
            {
                $this->calls[] = ['method' => $methodInfo->getName(), 'value' => $value];

                return is_array($value) && array_is_list($value) ? $value : [$value];
            }
        };
    }
}
