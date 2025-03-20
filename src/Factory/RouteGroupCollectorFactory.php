<?php

declare(strict_types=1);

namespace Dot\Router\Factory;

use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteGroupCollector;
use Dot\Router\RouteGroupCollectorInterface;
use Exception;
use Mezzio\Router\RouteCollectorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class RouteGroupCollectorFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): RouteGroupCollectorInterface
    {
        if (! $container->has(RouteCollectorInterface::class)) {
            throw new RuntimeException('Could not find "Mezzio\Router\RouteCollectorInterface" in container');
        }

        $routeCollector    = $container->get(RouteCollectorInterface::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);

        return new RouteGroupCollector($routeCollector, $middlewareFactory);
    }
}
