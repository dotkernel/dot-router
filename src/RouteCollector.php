<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Mezzio\Router\RouteCollectorInterface as MezzioRouteCollectorInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteCollector implements RouteCollectorInterface
{
    public function __construct(
        private readonly MezzioRouteCollectorInterface $routeCollector,
        private readonly MiddlewareFactory $middlewareFactory,
    ) {
    }

    public function group(
        string $prefix,
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware = null,
    ): RouteGroupCollectorInterface {
        return (new RouteGroupCollector($this->routeCollector, $this->middlewareFactory))
            ->setPrefix($prefix)
            ->setMiddleware($middleware);
    }

    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
    ): RouteCollectorInterface {
        $this->routeCollector->route($path, $this->middlewareFactory->prepare($middleware), $methods, $name);

        return $this;
    }

    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->any($path, $this->middlewareFactory->prepare($middleware), $name);

        return $this;
    }

    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->delete(
            $path,
            $this->middlewareFactory->prepare($middleware),
            $name
        );

        return $this;
    }

    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->get(
            $path,
            $this->middlewareFactory->prepare($middleware),
            $name
        );

        return $this;
    }

    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->patch(
            $path,
            $this->middlewareFactory->prepare($middleware),
            $name
        );

        return $this;
    }

    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->post(
            $path,
            $this->middlewareFactory->prepare($middleware),
            $name
        );

        return $this;
    }

    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
    ): RouteCollectorInterface {
        $this->routeCollector->put(
            $path,
            $this->middlewareFactory->prepare($middleware),
            $name
        );

        return $this;
    }

    /**
     * @return list<Route>
     */
    public function getRoutes(): array
    {
        return $this->routeCollector->getRoutes();
    }
}
