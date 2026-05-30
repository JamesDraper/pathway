<?php
declare(strict_types=1);

namespace Pathway;

/**
 * Migration interface for the Dispatcher class.
 *
 * This interface exists solely to support migration away from the Pathway package.
 *
 * Migration plan:
 * 1. Replace all references to \Pathway\Dispatcher \Pathway\DispatcherInterface.
 * 2. Introduce a new implementation of this interface which wraps the \Pathway\Dispatcher.
 * 3. Gradually migrate callers away from \Pathway\Dispatcher.
 * 4. Once no code depends on \Pathway\Dispatcher, replace all
 *    references to \Pathway\DispatcherInterface with your new dispatcher.
 * 5. Remove this package entirely.
 */
interface DispatcherInterface
{
    /**
     * @throws PathwayException
     */
    public function command(object $command): mixed;

    /**
     * @throws PathwayException
     */
    public function event(object $event): void;
}
