<?php
declare(strict_types=1);

namespace Tests\Fixtures\Internal\Info\ClassInfoTest\Nullable;

use function session_id;

final class NonNullableParameter
{
    /**
     * @return string|false
     */
    public function setSessionId(string $id)
    {
        return session_id($id);
    }
}
