<?php
declare(strict_types=1);

namespace Tests\Info\Unit\ClassInfoTest\Fixtures;

final class SimpleClass
{
    public function print(string $message): void
    {
        echo $message;
    }
}
