<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteCollector;
use Dot\Router\RouteGroupCollector;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteCollectorTest extends TestCase
{
    protected RouteCollector $routeCollector;
    protected RequestHandlerInterface $handler;

    /**
     * @throws Exception
     */
    protected function setup(): void
    {
        $this->routeCollector = new RouteCollector(
            new \Mezzio\Router\RouteCollector($this->createMock(RouterInterface::class)),
            $this->createMock(MiddlewareFactory::class)
        );
    }

    public function testWillCreateGroup(): void
    {
        $group = $this->routeCollector->group('/group', MiddlewareInterface::class);
        $this->assertInstanceOf(RouteGroupCollector::class, $group);
        $this->assertSame('/group', $group->getPrefix());
        $this->assertSame(MiddlewareInterface::class, $group->getMiddleware());
    }

    /**
     * @dataProvider routeProvider
     */
    public function testWillCreateRoute(
        string $routePath,
        ?string $routeName = null,
        ?string $generatedPath = null,
        ?string $generatedName = null,
        ?array $allowedMethods = null,
    ): void {
        $this->assertCount(0, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->route(
            $routePath,
            RequestHandlerInterface::class,
            $routeName,
            $allowedMethods
        );
        $this->assertInstanceOf(RouteCollector::class, $route);

        $this->assertCount(1, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertContainsOnlyInstancesOf(MiddlewareInterface::class, [$route->getMiddleware()]);
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
    }

    /**
     * @dataProvider methodProvider
     */
    public function testWillCreateRoutesByMethod(
        string $requestMethod,
        string $routePath,
        ?string $routeName = null,
        ?string $generatedPath = null,
        ?string $generatedName = null,
        ?array $allowedMethods = null,
    ): void {
        $this->assertCount(0, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->$requestMethod(
            $routePath,
            RequestHandlerInterface::class,
            $routeName,
        );
        $this->assertInstanceOf(RouteCollector::class, $route);

        $this->assertCount(1, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertContainsOnlyInstancesOf(MiddlewareInterface::class, [$route->getMiddleware()]);
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
    }

    public static function methodProvider(): array
    {
        return [
            [
                'requestMethod'  => 'any',
                'routePath'      => '/any',
                'routeName'      => null,
                'generatedPath'  => '/any',
                'generatedName'  => '/any',
                'allowedMethods' => null,
            ],
            [
                'requestMethod'  => 'any',
                'routePath'      => '/any',
                'routeName'      => 'app::any',
                'generatedPath'  => '/any',
                'generatedName'  => 'app::any',
                'allowedMethods' => null,
            ],
            [
                'requestMethod'  => 'delete',
                'routePath'      => '/delete',
                'routeName'      => null,
                'generatedPath'  => '/delete',
                'generatedName'  => '/delete^DELETE',
                'allowedMethods' => ['DELETE'],
            ],
            [
                'requestMethod'  => 'delete',
                'routePath'      => '/delete',
                'routeName'      => 'app::delete',
                'generatedPath'  => '/delete',
                'generatedName'  => 'app::delete',
                'allowedMethods' => ['DELETE'],
            ],
            [
                'requestMethod'  => 'get',
                'routePath'      => '/get',
                'routeName'      => null,
                'generatedPath'  => '/get',
                'generatedName'  => '/get^GET',
                'allowedMethods' => ['GET'],
            ],
            [
                'requestMethod'  => 'get',
                'routePath'      => '/get',
                'routeName'      => 'app::get',
                'generatedPath'  => '/get',
                'generatedName'  => 'app::get',
                'allowedMethods' => ['GET'],
            ],
            [
                'requestMethod'  => 'patch',
                'routePath'      => '/patch',
                'routeName'      => null,
                'generatedPath'  => '/patch',
                'generatedName'  => '/patch^PATCH',
                'allowedMethods' => ['PATCH'],
            ],
            [
                'requestMethod'  => 'patch',
                'routePath'      => '/patch',
                'routeName'      => 'app::patch',
                'generatedPath'  => '/patch',
                'generatedName'  => 'app::patch',
                'allowedMethods' => ['PATCH'],
            ],
            [
                'requestMethod'  => 'post',
                'routePath'      => '/post',
                'routeName'      => null,
                'generatedPath'  => '/post',
                'generatedName'  => '/post^POST',
                'allowedMethods' => ['POST'],
            ],
            [
                'requestMethod'  => 'post',
                'routePath'      => '/post',
                'routeName'      => 'app::post',
                'generatedPath'  => '/post',
                'generatedName'  => 'app::post',
                'allowedMethods' => ['POST'],
            ],
            [
                'requestMethod'  => 'put',
                'routePath'      => '/put',
                'routeName'      => null,
                'generatedPath'  => '/put',
                'generatedName'  => '/put^PUT',
                'allowedMethods' => ['PUT'],
            ],
            [
                'requestMethod'  => 'put',
                'routePath'      => '/put',
                'routeName'      => 'app::put',
                'generatedPath'  => '/put',
                'generatedName'  => 'app::put',
                'allowedMethods' => ['PUT'],
            ],
        ];
    }

    public static function routeProvider(): array
    {
        return [
            [
                'routePath'      => '/route',
                'routeName'      => null,
                'generatedPath'  => '/route',
                'generatedName'  => '/route',
                'allowedMethods' => null,
            ],
            [
                'routePath'      => '/route',
                'routeName'      => null,
                'generatedPath'  => '/route',
                'generatedName'  => '/route^DELETE:GET',
                'allowedMethods' => ['DELETE', 'GET'],
            ],
            [
                'routePath'      => '/route',
                'routeName'      => 'app::name',
                'generatedPath'  => '/route',
                'generatedName'  => 'app::name',
                'allowedMethods' => null,
            ],
            [
                'routePath'      => '/route',
                'routeName'      => 'app::name',
                'generatedPath'  => '/route',
                'generatedName'  => 'app::name',
                'allowedMethods' => ['DELETE', 'GET'],
            ],
        ];
    }
}
