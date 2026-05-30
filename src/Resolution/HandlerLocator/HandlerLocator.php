<?php
declare(strict_types=1);

namespace Pathway\Resolution\HandlerLocator;

interface HandlerLocator
{
    /**
     * @param string $id
     * @throws HandlerLocatorException
     */
    public function locate(string $id): object;
}
