<?php

declare(strict_types=1);

namespace Dot\Router\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

class MissingMiddlewareException extends RuntimeException implements NotFoundExceptionInterface
{
    public static function forService(string $id): self
    {
        return new self(sprintf(
            'Cannot fetch middleware service "%s"; service not registered,'
            . ' or does not resolve to an autoloadable class name',
            $id
        ));
    }
}
