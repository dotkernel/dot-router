<?php

declare(strict_types=1);

namespace Dot\Router;

use Exception;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function class_exists;
use function gettype;
use function is_object;
use function sprintf;

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
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): MiddlewareInterface
    {
        if (! $this->has($id)) {
            throw new Exception(sprintf(
                'Cannot fetch middleware service "%s"; service not registered,'
                . ' or does not resolve to an autoloadable class name',
                $id
            ));
        }

        $middleware = $this->container->has($id) ? $this->container->get($id) : new $id();
        if ($middleware instanceof RequestHandlerInterface && ! $middleware instanceof MiddlewareInterface) {
            $middleware = new RequestHandlerMiddleware($middleware);
        }

        if (! $middleware instanceof MiddlewareInterface) {
            throw new Exception(sprintf(
                'Service "%s" did not to resolve to a %s instance; resolved to "%s"',
                $id,
                MiddlewareInterface::class,
                is_object($middleware) ? $middleware::class : gettype($middleware)
            ));
        }

        return $middleware;
    }
}
