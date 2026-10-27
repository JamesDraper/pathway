<?php

declare(strict_types=1);

namespace Tests\Info\Method\Fixtures;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
