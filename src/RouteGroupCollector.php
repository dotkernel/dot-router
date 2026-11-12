<?php

declare(strict_types=1);

namespace Dot\Router;

use Dot\Router\Exception\InvalidArgumentException;
use Mezzio\Router\Route;
use Mezzio\Router\RouteCollectorInterface as MezzioRouteCollectorInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_filter;
use function array_values;
use function in_array;
use function is_object;
use function is_string;

/**
 * @phpstan-import-type MiddlewareSpec from RouteCollectorInterface
 */
class RouteGroupCollector implements RouteGroupCollectorInterface
{
    protected string $prefix = '';
    /** @var MiddlewareSpec|null */
    protected mixed $middleware;

    public function __construct(
        private readonly MezzioRouteCollectorInterface $routeCollector,
        private readonly MiddlewareFactory $middlewareFactory,
    ) {
        $this->middleware = null;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return MiddlewareSpec|null
     */
    public function getMiddleware(): mixed
    {
        return $this->middleware;
    }

    /**
     * @param MiddlewareSpec|null $middleware
     */
    public function setMiddleware(
        null|string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
    ): self {
        $this->middleware = $middleware;

        return $this;
    }

    /**
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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->route(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $methods,
            $name
        );

        return $this;
    }

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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->any(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->delete(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->get(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->patch(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

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
    ): RouteGroupCollectorInterface {
        $this->routeCollector->post(
            $this->preparePath($path),
            $this->prepareMiddleware($middleware, $excludeMiddleware),
            $name
        );

        return $this;
    }

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

    /**
     * @return non-empty-string
     * @throws InvalidArgumentException When both the prefix and the path are empty.
     */
    private function preparePath(string $path): string
    {
        $path = $this->prefix . $path;
        if ($path === '') {
            throw InvalidArgumentException::emptyPath();
        }

        return $path;
    }

    /**
     * Group middleware runs first, minus any listed in $excludeMiddleware, then the route's own middleware.
     *
     * @param MiddlewareSpec $middleware
     * @param class-string|list<class-string> $excludeMiddleware
     */
    private function prepareMiddleware(
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware,
        array|string $excludeMiddleware = [],
    ): MiddlewareInterface {
        $exclude = is_string($excludeMiddleware) ? [$excludeMiddleware] : $excludeMiddleware;

        $groupMiddleware = match (true) {
            $this->middleware === null => [],
            MiddlewareFactory::isMiddlewareList($this->middleware) => $this->middleware,
            default => [$this->middleware],
        };
        $groupMiddleware = array_filter(
            $groupMiddleware,
            fn (mixed $item): bool => ! $this->isExcluded($item, $exclude)
        );

        $routeMiddleware = MiddlewareFactory::isMiddlewareList($middleware) ? $middleware : [$middleware];

        return $this->middlewareFactory->prepare([
            ...array_values($groupMiddleware),
            ...$routeMiddleware,
        ]);
    }

    /**
     * @param list<class-string> $exclude
     */
    private function isExcluded(mixed $item, array $exclude): bool
    {
        if (is_string($item)) {
            return in_array($item, $exclude, true);
        }

        if (is_object($item)) {
            foreach ($exclude as $class) {
                if ($item instanceof $class) {
                    return true;
                }
            }
        }

        return false;
    }
}
