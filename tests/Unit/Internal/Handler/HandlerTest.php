<?php
declare(strict_types=1);

namespace Tests\Unit\Internal\Handler;

use Pathway\Internal\Handler\Handler;
use Pathway\Internal\Handler\Method;
use Pathway\DispatcherInterface;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

use Mockery;

use LogicException;
use stdClass;

final class HandlerTest extends TestCase
{
    #[Test]
    public function it_runs_prepare_process_and_finalize(): void
    {
        $message = new stdClass();

        $dispatcher = Mockery::mock(DispatcherInterface::class);

        $prepare = Mockery::mock(Method::class);
        $process = Mockery::mock(Method::class);
        $finalize = Mockery::mock(Method::class);

        $prepare
            ->expects()
            ->invoke([$message, $dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andReturn(['e' => 'f', 'g' => 'h']);


        $finalize
            ->expects()
            ->invoke(['e' => 'f', 'g' => 'h'])
            ->andReturn(['i' => 'j', 'k' => 'l']);

        $handler = new Handler($prepare, $process, $finalize);

        $result = $handler->handle($message, $dispatcher);

        $this->assertSame(['i' => 'j', 'k' => 'l'], $result);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_prepare_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $dispatcher = Mockery::mock(DispatcherInterface::class);

        $prepare = Mockery::mock(Method::class);
        $process = Mockery::mock(Method::class);
        $finalize = Mockery::mock(Method::class);

        $prepare
            ->expects()
            ->invoke([$message, $dispatcher])
            ->andThrows(new LogicException);

        $handler = new Handler($prepare, $process, $finalize);

        $handler->handle($message, $dispatcher);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_process_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $dispatcher = Mockery::mock(DispatcherInterface::class);

        $prepare = Mockery::mock(Method::class);
        $process = Mockery::mock(Method::class);
        $finalize = Mockery::mock(Method::class);

        $prepare
            ->expects()
            ->invoke([$message, $dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andThrows(new LogicException);


        $handler = new Handler($prepare, $process, $finalize);

        $handler->handle($message, $dispatcher);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_finalize_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $dispatcher = Mockery::mock(DispatcherInterface::class);

        $prepare = Mockery::mock(Method::class);
        $process = Mockery::mock(Method::class);
        $finalize = Mockery::mock(Method::class);

        $prepare
            ->expects()
            ->invoke([$message, $dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andReturn(['e' => 'f', 'g' => 'h']);


        $finalize
            ->expects()
            ->invoke(['e' => 'f', 'g' => 'h'])
            ->andThrows(new LogicException);

        $handler = new Handler($prepare, $process, $finalize);

        $handler->handle($message, $dispatcher);
    }
}
