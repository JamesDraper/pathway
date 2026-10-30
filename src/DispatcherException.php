<?php

declare(strict_types=1);

namespace Pathway;

use Pathway\Invocation\InvocationException;

use LogicException;

final class DispatcherException extends LogicException implements InvocationException {}
