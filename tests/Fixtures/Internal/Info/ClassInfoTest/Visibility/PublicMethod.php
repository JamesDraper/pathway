<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Visibility;

final class PublicMethod
{
    public function greetPublic(): void
    {
        echo 'Hello World!';
    }
}
