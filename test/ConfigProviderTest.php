<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\ConfigProvider;
use Dot\Router\Factory\MiddlewareContainerFactory;
use Dot\Router\Factory\MiddlewareFactoryFactory;
use Dot\Router\Factory\RouteCollectorFactory;
use Dot\Router\Factory\RouteGroupCollectorFactory;
use Dot\Router\MiddlewareContainer;
use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteCollector;
use Dot\Router\RouteCollectorInterface;
use Dot\Router\RouteGroupCollector;
use Dot\Router\RouteGroupCollectorInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $aliases = $this->config['dependencies']['aliases'];
        $this->assertArrayHasKey(RouteCollectorInterface::class, $aliases);
        $this->assertSame(RouteCollector::class, $aliases[RouteCollectorInterface::class]);
        $this->assertArrayHasKey(RouteGroupCollectorInterface::class, $aliases);
        $this->assertSame(RouteGroupCollector::class, $aliases[RouteGroupCollectorInterface::class]);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $factories = $this->config['dependencies']['factories'];
        $this->assertArrayHasKey(RouteCollector::class, $factories);
        $this->assertSame(RouteCollectorFactory::class, $factories[RouteCollector::class]);
        $this->assertArrayHasKey(RouteGroupCollector::class, $factories);
        $this->assertSame(RouteGroupCollectorFactory::class, $factories[RouteGroupCollector::class]);
        $this->assertArrayHasKey(MiddlewareContainer::class, $factories);
        $this->assertSame(MiddlewareContainerFactory::class, $factories[MiddlewareContainer::class]);
        $this->assertArrayHasKey(MiddlewareFactory::class, $factories);
        $this->assertSame(MiddlewareFactoryFactory::class, $factories[MiddlewareFactory::class]);
    }
}
