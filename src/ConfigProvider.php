<?php

declare(strict_types=1);

namespace Dot\Router;

use Dot\Router\Factory\MiddlewareContainerFactory;
use Dot\Router\Factory\MiddlewareFactoryFactory;
use Dot\Router\Factory\RouteCollectorFactory;
use Dot\Router\Factory\RouteGroupCollectorFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getDependencyConfig(): array
    {
        return [
            'aliases'   => [
                RouteCollectorInterface::class      => RouteCollector::class,
                RouteGroupCollectorInterface::class => RouteGroupCollector::class,
            ],
            'factories' => [
                RouteCollector::class      => RouteCollectorFactory::class,
                RouteGroupCollector::class => RouteGroupCollectorFactory::class,
                MiddlewareContainer::class => MiddlewareContainerFactory::class,
                MiddlewareFactory::class   => MiddlewareFactoryFactory::class,
            ],
        ];
    }
}
