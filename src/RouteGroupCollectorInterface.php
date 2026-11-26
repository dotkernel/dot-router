<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @phpstan-import-type MiddlewareSpec from RouteCollectorInterface
 */
interface RouteGroupCollectorInterface
{
    public function getPrefix(): string;

    public function setPrefix(string $prefix): self;

    /**
     * @return MiddlewareSpec|null
     */
    public function getMiddleware(): mixed;

    /**
     * @param MiddlewareSpec|null $middleware
     */
    public function setMiddleware(
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
    ): self;

    /**
     * Group middleware listed in $excludeMiddleware is not run for this route.
     *
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param list<string>|null $methods
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface;

    /**
     * Returns every route in the underlying Mezzio route collector, not only this group's routes.
     *
     * @return list<Route>
     */
    public function getRoutes(): array;
}
