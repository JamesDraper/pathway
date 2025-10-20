<?php
declare(strict_types=1);

namespace Tests\HandlerRunner;

use Pathway\HandlerRunner\HandlerRunner;
use Pathway\HandlerRunner\Method;
use Pathway\DispatcherInterface;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;

use Mockery\MockInterface;
use Mockery;

use LogicException;
use stdClass;

final class HandlerRunnerTest extends TestCase
{
    private readonly HandlerRunner $handlerRunner;

    private readonly DispatcherInterface&MockInterface $dispatcher;

    private readonly Method&MockInterface $prepare;

    private readonly Method&MockInterface $process;

    private readonly Method&MockInterface $finalize;

    #[Test]
    public function it_runs_prepare_process_and_finalize(): void
    {
        $message = new stdClass();

        $this
            ->prepare
            ->expects()
            ->invoke([$message, $this->dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $this
            ->process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andReturn(['e' => 'f', 'g' => 'h']);


        $this
            ->finalize
            ->expects()
            ->invoke(['e' => 'f', 'g' => 'h'])
            ->andReturn(['i' => 'j', 'k' => 'l']);

        $result = $this->handlerRunner->run($message, $this->dispatcher);

        $this->assertSame(['i' => 'j', 'k' => 'l'], $result);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_prepare_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $this
            ->prepare
            ->expects()
            ->invoke([$message, $this->dispatcher])
            ->andThrows(new LogicException);

        $this->handlerRunner->run($message, $this->dispatcher);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_process_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $this
            ->prepare
            ->expects()
            ->invoke([$message, $this->dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $this
            ->process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andThrows(new LogicException);


        $this->handlerRunner->run($message, $this->dispatcher);
    }

    #[Test]
    #[Depends('it_runs_prepare_process_and_finalize')]
    public function exceptions_from_finalize_are_thrown_upwards(): void
    {
        $this->expectException(LogicException::class);

        $message = new stdClass();

        $this
            ->prepare
            ->expects()
            ->invoke([$message, $this->dispatcher])
            ->andReturn(['a' => 'b', 'c' => 'd']);

        $this
            ->process
            ->expects()
            ->invoke(['a' => 'b', 'c' => 'd'])
            ->andReturn(['e' => 'f', 'g' => 'h']);


        $this
            ->finalize
            ->expects()
            ->invoke(['e' => 'f', 'g' => 'h'])
            ->andThrows(new LogicException);

        $this->handlerRunner->run($message, $this->dispatcher);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = Mockery::mock(DispatcherInterface::class);

        $this->prepare = Mockery::mock(Method::class);
        $this->process = Mockery::mock(Method::class);
        $this->finalize = Mockery::mock(Method::class);

        $this->handlerRunner = new HandlerRunner($this->prepare, $this->process, $this->finalize);
    }
}
