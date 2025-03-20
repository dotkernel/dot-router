<?php

declare(strict_types=1);

namespace Dot\Router\Factory;

use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteCollector;
use Dot\Router\RouteCollectorInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class RouteCollectorFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): RouteCollectorInterface
    {
        if (! $container->has(\Mezzio\Router\RouteCollectorInterface::class)) {
            throw new RuntimeException('Could not find "Mezzio\Router\RouteCollectorInterface" in container');
        }

        $routeCollector    = $container->get(\Mezzio\Router\RouteCollectorInterface::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);

        return new RouteCollector($routeCollector, $middlewareFactory);
    }
}
