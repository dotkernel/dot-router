<?php

declare(strict_types=1);

namespace Dot\Router\Factory;

use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteGroupCollector;
use Dot\Router\RouteGroupCollectorInterface;
use Mezzio\Router\RouteCollectorInterface as MezzioRouteCollectorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

class RouteGroupCollectorFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RuntimeException
     */
    public function __invoke(ContainerInterface $container): RouteGroupCollectorInterface
    {
        if (! $container->has(MezzioRouteCollectorInterface::class)) {
            throw new RuntimeException(
                sprintf('Could not find "%s" in container', MezzioRouteCollectorInterface::class)
            );
        }

        $routeCollector    = $container->get(MezzioRouteCollectorInterface::class);
        $middlewareFactory = $container->get(MiddlewareFactory::class);

        return new RouteGroupCollector($routeCollector, $middlewareFactory);
    }
}
