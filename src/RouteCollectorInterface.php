<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @phpstan-type MiddlewareItem string|callable|MiddlewareInterface|RequestHandlerInterface
 * @phpstan-type MiddlewareSpec MiddlewareItem|list<MiddlewareItem>
 */
interface RouteCollectorInterface
{
    /**
     * @param MiddlewareSpec|null $middleware
     */
    public function group(
        string $prefix,
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware = null,
    ): RouteGroupCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     * @param list<string>|null $methods
     */
    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @param non-empty-string $path
     * @param MiddlewareSpec $middleware
     * @param non-empty-string|null $name
     */
    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    /**
     * @return list<Route>
     */
    public function getRoutes(): array;
}
