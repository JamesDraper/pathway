<?php
declare(strict_types=1);

namespace Tests\Internal\Reflection;

use Pathway\Internal\Reflection\HandlerMethod;
use Pathway\Internal\Reflection\HandlerClass;
use Pathway\DispatcherInterface;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\Test;

use Mockery;

use stdClass;

final class HandlerClassTest extends TestCase
{
    #[Test]
    public function itRunsPrepareProcessAndFinalize(): void
    {
        $message = new stdClass();

        $dispatcher = Mockery::mock(DispatcherInterface::class);

        $prepare = Mockery::mock(HandlerMethod::class);
        $process = Mockery::mock(HandlerMethod::class);
        $finalize = Mockery::mock(HandlerMethod::class);

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

        $handler = new HandlerClass($prepare, $process, $finalize);

        $result = $handler->handle($message, $dispatcher);

        $this->assertSame(['i' => 'j', 'k' => 'l'], $result);
    }
}
