<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteGroupCollectorInterface
{
    public function getPrefix(): string;

    /**
     * @param non-empty-string $prefix
     */
    public function setPrefix(string $prefix): self;

    public function getMiddleware(): mixed;

    public function setMiddleware(
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
    ): self;

    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @return list<Route>
     */
    public function getRoutes(): array;
}
