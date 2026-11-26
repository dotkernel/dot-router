<?php

declare(strict_types=1);

namespace Dot\Router\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function emptyPipeline(): self
    {
        return new self('Cannot create a middleware pipeline from an empty list of middleware');
    }

    public static function emptyPath(): self
    {
        return new self('Cannot add a route with an empty path; set a group prefix or a route path');
    }
}
