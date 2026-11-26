<?php

declare(strict_types=1);

namespace Dot\Router;

use ArgumentCountError;
use Dot\Router\Exception\InvalidMiddlewareException;
use Dot\Router\Exception\MissingMiddlewareException;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function class_exists;

class MiddlewareContainer implements ContainerInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function has(string $id): bool
    {
        if ($this->container->has($id)) {
            return true;
        }

        return class_exists($id);
    }

    /**
     * @throws MissingMiddlewareException
     * @throws InvalidMiddlewareException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): MiddlewareInterface
    {
        if (! $this->has($id)) {
            throw MissingMiddlewareException::forService($id);
        }

        if ($this->container->has($id)) {
            $middleware = $this->container->get($id);
        } else {
            try {
                $middleware = new $id();
            } catch (ArgumentCountError $exception) {
                throw InvalidMiddlewareException::notInstantiable($id, $exception);
            }
        }

        if ($middleware instanceof RequestHandlerInterface && ! $middleware instanceof MiddlewareInterface) {
            $middleware = new RequestHandlerMiddleware($middleware);
        }

        if (! $middleware instanceof MiddlewareInterface) {
            throw InvalidMiddlewareException::forService($id, $middleware);
        }

        return $middleware;
    }
}
