<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Visibility;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
