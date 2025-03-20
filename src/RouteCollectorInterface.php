<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteCollectorInterface
{
    public function group(
        string $prefix,
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware = null,
    ): RouteGroupCollectorInterface;

    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
    ): RouteCollectorInterface;

    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface;

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
