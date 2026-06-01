<?php
declare(strict_types=1);

namespace Tests\Info\Integration\Fixtures\Type;

use DateTime;

final class ClassParameter
{
    public function format(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }
}
