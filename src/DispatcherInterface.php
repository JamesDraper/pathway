<?php

declare(strict_types=1);

namespace Pathway;

/**
 * Exists only to make moving away from Pathway easier.
 *
 * 1. Replace all references to Dispatcher in your code with DispatcherInterface.
 * 2. Write your own dispatcher that implements DispatcherInterface.
 * 3. Wherever Dispatcher is constructed or registered (e.g. your DI container),
 *     use your implementation instead.
 * 4. Replace references to DispatcherInterface with your own implementation.
 * 5. Remove `implements DispatcherInterface` from your implementation.
 * 6. Remove Pathway.
 */
interface DispatcherInterface
{
    public function dispatch(object $command): mixed;
}
