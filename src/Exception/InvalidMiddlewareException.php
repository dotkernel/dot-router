<?php

declare(strict_types=1);

namespace Dot\Router\Exception;

use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use Throwable;

use function get_debug_type;
use function sprintf;

class InvalidMiddlewareException extends RuntimeException implements ContainerExceptionInterface
{
    public static function forService(string $id, mixed $resolved): self
    {
        return new self(sprintf(
            'Service "%s" did not resolve to a %s instance; resolved to "%s"',
            $id,
            MiddlewareInterface::class,
            get_debug_type($resolved)
        ));
    }

    public static function notInstantiable(string $id, Throwable $previous): self
    {
        return new self(
            sprintf(
                'Cannot instantiate middleware "%s" without arguments; register it in the container',
                $id
            ),
            0,
            $previous
        );
    }
}
