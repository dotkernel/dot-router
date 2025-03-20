<?php

declare(strict_types=1);

namespace Dot\Router\Factory;

use Dot\Router\MiddlewareContainer;
use Dot\Router\MiddlewareFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MiddlewareFactoryFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): MiddlewareFactory
    {
        return new MiddlewareFactory(
            $container->get(MiddlewareContainer::class),
        );
    }
}
