<?php
declare(strict_types=1);

namespace Pathway;

interface EventHandlerResolverInterface
{
    /**
     * @param class-string $eventClassPath
     * @return object[]
     */
    public function resolve(string $eventClassPath): array;
}
