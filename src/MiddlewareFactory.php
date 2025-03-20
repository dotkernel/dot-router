<?php

declare(strict_types=1);

namespace Dot\Router;

use Dot\Router\Middleware\LazyLoadingMiddleware;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_shift;
use function count;
use function is_array;
use function is_callable;

class MiddlewareFactory
{
    public function __construct(
        private readonly MiddlewareContainer $container,
    ) {
    }

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
            return $this->pipeline(...$middleware);
        }

        return $this->lazy($middleware);
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
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface ...$middleware
     */
    public function pipeline(...$middleware): MiddlewarePipe
    {
        if (is_array($middleware[0]) && count($middleware) === 1) {
            $middleware = array_shift($middleware);
        }

        $pipeline = new MiddlewarePipe();
        foreach ($middleware as $m) {
            $pipeline->pipe($this->prepare($m));
        }

        return $pipeline;
    }
}
