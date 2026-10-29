<?php

declare(strict_types=1);

namespace Tests\Defaults\DefaultHandlerLocatorTest;

use Pathway\Resolution\DefaultHandlerLocatorException;
use Pathway\Resolution\HandlerLocatorException;
use Pathway\Resolution\DefaultHandlerLocator;

use Tests\Defaults\DefaultHandlerLocatorTest\Fixtures;
use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

use RuntimeException;
use stdClass;

final class DefaultHandlerLocatorTest extends TestCase
{
    private readonly DefaultHandlerLocator $defaultHandlerLocator;

    #[Test]
    public function it_exists(): void
    {
        $this->assertClassExists(DefaultHandlerLocator::class);
    }

    #[Test]
    public function its_exception_implements_the_handler_locator_exception_interface(): void
    {
        $this->assertImplementsInterface(DefaultHandlerLocatorException::class, HandlerLocatorException::class);
    }

    #[Test]
    public function it_returns_the_handler_made_by_the_factory_method(): void
    {
        $handler = $this->defaultHandlerLocator->locate(Fixtures\ValidHandler::class);

        $this->assertInstanceOf(Fixtures\ValidHandler::class, $handler);
    }

    #[Test]
    public function it_allows_optional_and_variadic_parameters_on_the_factory_method(): void
    {
        $handler = $this->defaultHandlerLocator->locate(Fixtures\OptionalParametersHandler::class);

        $this->assertInstanceOf(Fixtures\OptionalParametersHandler::class, $handler);
        $this->assertSame('default', $handler->optional);
        $this->assertSame([], $handler->rest);
    }

    #[Test]
    public function it_allows_the_factory_method_to_return_a_subclass(): void
    {
        $handler = $this->defaultHandlerLocator->locate(Fixtures\ParentHandler::class);

        $this->assertInstanceOf(Fixtures\ChildHandler::class, $handler);
    }

    #[Test]
    #[DataProvider('provider_it_throws_if_the_handler_cannot_be_located')]
    public function it_throws_if_the_handler_cannot_be_located(string $id, string $expectedMessage): void
    {
        $this->expectException(DefaultHandlerLocatorException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->defaultHandlerLocator->locate($id);
    }

    public static function provider_it_throws_if_the_handler_cannot_be_located(): array
    {
        return [
            'class does not exist' => [
                'id' => 'Some\\MissingHandler',
                'expectedMessage' => 'Handler class "Some\\MissingHandler" does not exist.',
            ],
            'id is an interface' => [
                'id' => Fixtures\NotAHandlerInterface::class,
                'expectedMessage' => 'Handler class "' . Fixtures\NotAHandlerInterface::class . '" does not exist.',
            ],
            'factory method does not exist' => [
                'id' => Fixtures\NoFactoryMethodHandler::class,
                'expectedMessage' => 'Handler class "' . Fixtures\NoFactoryMethodHandler::class . '" does not have a "make" method.',
            ],
            'factory method is protected' => [
                'id' => Fixtures\ProtectedFactoryMethodHandler::class,
                'expectedMessage' => 'Method "' . Fixtures\ProtectedFactoryMethodHandler::class . '::make" must be public and static.',
            ],
            'factory method is private' => [
                'id' => Fixtures\PrivateFactoryMethodHandler::class,
                'expectedMessage' => 'Method "' . Fixtures\PrivateFactoryMethodHandler::class . '::make" must be public and static.',
            ],
            'factory method is not static' => [
                'id' => Fixtures\NonStaticFactoryMethodHandler::class,
                'expectedMessage' => 'Method "' . Fixtures\NonStaticFactoryMethodHandler::class . '::make" must be public and static.',
            ],
            'factory method has a required parameter' => [
                'id' => Fixtures\RequiredParameterHandler::class,
                'expectedMessage' => 'Method "' . Fixtures\RequiredParameterHandler::class . '::make" must not have required parameters.',
            ],
        ];
    }

    #[Test]
    public function it_wraps_exceptions_thrown_by_the_factory_method(): void
    {
        try {
            $this->defaultHandlerLocator->locate(Fixtures\ThrowingHandler::class);
        } catch (DefaultHandlerLocatorException $exception) {
            $this->assertSame(
                'Method "' . Fixtures\ThrowingHandler::class . '::make" threw an exception: Something went wrong.',
                $exception->getMessage(),
            );

            $previous = $exception->getPrevious();

            $this->assertInstanceOf(RuntimeException::class, $previous);
            $this->assertSame('Something went wrong.', $previous->getMessage());

            return;
        }

        $this->fail('Expected a DefaultHandlerLocatorException to be thrown.');
    }

    #[Test]
    #[DataProvider('provider_it_throws_if_the_factory_method_returns_an_invalid_type')]
    public function it_throws_if_the_factory_method_returns_an_invalid_type(mixed $returnValue, string $expectedType): void
    {
        Fixtures\InvalidReturnHandler::$returnValue = $returnValue;

        $this->expectException(DefaultHandlerLocatorException::class);
        $this->expectExceptionMessage(
            'Method "' . Fixtures\InvalidReturnHandler::class . '::make" must return an instance of "'
            . Fixtures\InvalidReturnHandler::class . '", "' . $expectedType . '" returned.',
        );

        $this->defaultHandlerLocator->locate(Fixtures\InvalidReturnHandler::class);
    }

    public static function provider_it_throws_if_the_factory_method_returns_an_invalid_type(): array
    {
        return [
            'null' => [
                'returnValue' => null,
                'expectedType' => 'null',
            ],
            'string' => [
                'returnValue' => 'handler',
                'expectedType' => 'string',
            ],
            'int' => [
                'returnValue' => 1,
                'expectedType' => 'int',
            ],
            'other object' => [
                'returnValue' => new stdClass(),
                'expectedType' => 'object(stdClass)',
            ],
            'other handler' => [
                'returnValue' => new Fixtures\ValidHandler(),
                'expectedType' => 'object(' . Fixtures\ValidHandler::class . ')',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultHandlerLocator = new DefaultHandlerLocator();
    }
}
