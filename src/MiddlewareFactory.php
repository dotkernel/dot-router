<?php

declare(strict_types=1);

namespace Dot\Router;

use Dot\Router\Exception\InvalidArgumentException;
use Dot\Router\Middleware\LazyLoadingMiddleware;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function is_array;
use function is_callable;

/**
 * @phpstan-import-type MiddlewareItem from RouteCollectorInterface
 * @phpstan-import-type MiddlewareSpec from RouteCollectorInterface
 */
class MiddlewareFactory
{
    public function __construct(
        private readonly MiddlewareContainer $container,
    ) {
    }

    /**
     * @param MiddlewareSpec $middleware
     * @throws InvalidArgumentException When given an empty list.
     */
    public function prepare(
        string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
    ): MiddlewareInterface {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        if ($middleware instanceof RequestHandlerInterface) {
            return $this->handler($middleware);
        }

        if (is_callable($middleware)) {
            return $this->callable($middleware);
        }

        if (is_array($middleware)) {
            return $this->pipeline($middleware);
        }

        return $this->lazy($middleware);
    }

    /**
     * True for an array of middleware; false for a callable array such as [$object, 'method'].
     *
     * @internal
     *
     * @param MiddlewareSpec|null $middleware
     * @phpstan-assert-if-true list<MiddlewareItem> $middleware
     */
    public static function isMiddlewareList(mixed $middleware): bool
    {
        return is_array($middleware) && ! is_callable($middleware);
    }

    public function callable(callable $middleware): CallableMiddlewareDecorator
    {
        return new CallableMiddlewareDecorator($middleware);
    }

    public function handler(RequestHandlerInterface $handler): RequestHandlerMiddleware
    {
        return new RequestHandlerMiddleware($handler);
    }

    public function lazy(string $middleware): LazyLoadingMiddleware
    {
        return new LazyLoadingMiddleware($this->container, $middleware);
    }

    /**
     * Accepts either several middleware arguments, or a single list of middleware.
     *
     * @param MiddlewareSpec ...$middleware
     * @throws InvalidArgumentException When given an empty list.
     */
    public function pipeline(
        string|array|callable|MiddlewareInterface|RequestHandlerInterface ...$middleware
    ): MiddlewarePipe {
        if (count($middleware) === 1 && isset($middleware[0]) && self::isMiddlewareList($middleware[0])) {
            $middleware = $middleware[0];
        }

        if ($middleware === []) {
            throw InvalidArgumentException::emptyPipeline();
        }

        $pipeline = new MiddlewarePipe();
        foreach ($middleware as $item) {
            $pipeline->pipe($this->prepare($item));
        }

        return $pipeline;
    }
}
