<?php

declare(strict_types=1);

namespace Dot\Router;

use Mezzio\Router\Route;
use Mezzio\Router\RouteCollectorInterface as MezzioRouteCollectorInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_merge;
use function in_array;
use function is_array;
use function is_callable;
use function is_string;
use function sprintf;

class RouteGroupCollector implements RouteGroupCollectorInterface
{
    protected string $prefix = '';
    protected mixed $middleware;

    public function __construct(
        private readonly MezzioRouteCollectorInterface $routeCollector,
        private readonly MiddlewareFactory $middlewareFactory,
    ) {
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param non-empty-string $prefix
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getMiddleware(): mixed
    {
        return $this->middleware;
    }

    public function setMiddleware(
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
    ): self {
        $this->middleware = $middleware;

        return $this;
    }

    public function route(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        ?array $methods = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->route(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $methods,
            $name
        );

        return $this;
    }

    public function any(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->any(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

    public function delete(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->delete(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

    public function get(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->get(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

    public function patch(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->patch(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

    public function post(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->post(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

    public function put(
        string $path,
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        ?string $name = null,
        array|string $excludeMiddleware = [],
    ): RouteGroupCollectorInterface {
        $this->routeCollector->put(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
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

    private function preparePath(string $path): string
    {
        return sprintf('%s%s', $this->prefix, $path);
    }

    /**
     * @param class-string|list<class-string> $excludeMiddleware
     */
    public function prepareMiddleware(
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        array|string $excludeMiddleware = [],
    ): MiddlewareInterface {
        if (is_string($excludeMiddleware)) {
            $excludeMiddleware = [$excludeMiddleware];
        }

        $prependMiddleware = [];
        if ($this->middleware !== null) {
            $prependMiddleware = $this->middleware;
            if (! is_array($prependMiddleware)) {
                $prependMiddleware = [$prependMiddleware];
            }
        }

        if (is_callable($middleware)) {
            return $this->middlewareFactory->prepare($middleware);
        }

        if (! is_array($middleware)) {
            $middleware = [$middleware];
        }

        $middleware = array_merge($prependMiddleware, $middleware);
        $middleware = array_filter(
            $middleware,
            function (callable|string|MiddlewareInterface|RequestHandlerInterface $item) use ($excludeMiddleware) {
                if (is_callable($item)) {
                    return true;
                }
                if ($item instanceof RequestHandlerInterface) {
                    $item = RequestHandlerInterface::class;
                }
                if ($item instanceof MiddlewareInterface) {
                    $item = MiddlewareInterface::class;
                }
                return ! in_array($item, $excludeMiddleware, true);
            }
        );

        return $this->middlewareFactory->prepare($middleware);
    }
}
