<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\TypeFormatterTest;

enum SimpleBackedEnum: string
{
    case ONE = 'TWO';
    case THREE = 'FOUR';
}
