<?php
declare(strict_types=1);

namespace Pathway;

interface CommandHandlerResolverInterface
{
    /**
     * @param class-string $commandClassPath
     */
    public function resolve(string $commandClassPath): object;
}
