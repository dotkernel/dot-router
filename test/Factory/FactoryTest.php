<?php

declare(strict_types=1);

namespace DotTest\Router\Factory;

use Dot\Router\Factory\MiddlewareContainerFactory;
use Dot\Router\Factory\MiddlewareFactoryFactory;
use Dot\Router\Factory\RouteCollectorFactory;
use Dot\Router\Factory\RouteGroupCollectorFactory;
use Dot\Router\MiddlewareContainer;
use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteCollector;
use Dot\Router\RouteGroupCollector;
use DotTest\Router\Fixture\InMemoryContainer;
use Mezzio\Router\RouteCollector as MezzioRouteCollector;
use Mezzio\Router\RouteCollectorInterface as MezzioRouteCollectorInterface;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class FactoryTest extends TestCase
{
    private InMemoryContainer $container;

    protected function setUp(): void
    {
        $this->container     = new InMemoryContainer();
        $middlewareContainer = (new MiddlewareContainerFactory())($this->container);
        $this->container->set(MiddlewareContainer::class, $middlewareContainer);
        $this->container->set(MiddlewareFactory::class, (new MiddlewareFactoryFactory())($this->container));
    }

    public function testMiddlewareContainerFactory(): void
    {
        $this->assertInstanceOf(MiddlewareContainer::class, $this->container->get(MiddlewareContainer::class));
    }

    public function testMiddlewareFactoryFactory(): void
    {
        $this->assertInstanceOf(MiddlewareFactory::class, $this->container->get(MiddlewareFactory::class));
    }

    public function testRouteCollectorFactory(): void
    {
        $this->registerMezzioCollector();

        $this->assertInstanceOf(RouteCollector::class, (new RouteCollectorFactory())($this->container));
    }

    public function testRouteGroupCollectorFactory(): void
    {
        $this->registerMezzioCollector();

        $group = (new RouteGroupCollectorFactory())($this->container);

        $this->assertInstanceOf(RouteGroupCollector::class, $group);
        $this->assertNull($group->getMiddleware());
    }

    /**
     * @param class-string<RouteCollectorFactory|RouteGroupCollectorFactory> $factory
     */
    #[DataProvider('collectorFactoryProvider')]
    public function testCollectorFactoryRequiresMezzioRouteCollector(string $factory): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(MezzioRouteCollectorInterface::class);

        (new $factory())($this->container);
    }

    /**
     * @return array<string, array{factory: class-string<RouteCollectorFactory|RouteGroupCollectorFactory>}>
     */
    public static function collectorFactoryProvider(): array
    {
        return [
            'RouteCollectorFactory'      => ['factory' => RouteCollectorFactory::class],
            'RouteGroupCollectorFactory' => ['factory' => RouteGroupCollectorFactory::class],
        ];
    }

    private function registerMezzioCollector(): void
    {
        $this->container->set(
            MezzioRouteCollectorInterface::class,
            new MezzioRouteCollector($this->createStub(RouterInterface::class))
        );
    }
}
