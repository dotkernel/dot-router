<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\MiddlewareFactory;
use Dot\Router\RouteGroupCollector;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteGroupCollectorTest extends TestCase
{
    protected RouteGroupCollector $routeGroupCollector;
    protected RequestHandlerInterface $handler;

    /**
     * @throws Exception
     */
    protected function setup(): void
    {
        $this->routeGroupCollector = new RouteGroupCollector(
            new RouteCollector($this->createMock(RouterInterface::class)),
            $this->createMock(MiddlewareFactory::class)
        );
    }

    public function testWillCreateGroup(): void
    {
        $group = $this->routeGroupCollector->setPrefix('/group')->setMiddleware(MiddlewareInterface::class);
        $this->assertSame('/group', $group->getPrefix());
        $this->assertSame(MiddlewareInterface::class, $group->getMiddleware());
    }

    /**
     * @dataProvider routeProvider
     */
    public function testWillCreateRoute(
        string $groupPrefix,
        string $routePath,
        ?string $routeName = null,
        ?string $generatedPath = null,
        ?string $generatedName = null,
        ?array $allowedMethods = null,
        array|null|string $excludeMiddleware = [],
    ): void {
        $this->assertCount(0, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector
            ->setPrefix($groupPrefix)
            ->setMiddleware(MiddlewareInterface::class)
            ->route(
                $routePath,
                RequestHandlerInterface::class,
                $routeName,
                $allowedMethods,
                $excludeMiddleware
            );
        $this->assertInstanceOf(RouteGroupCollector::class, $route);

        $this->assertCount(1, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertContainsOnlyInstancesOf(MiddlewareInterface::class, [$route->getMiddleware()]);
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
    }

    /**
     * @dataProvider methodProvider
     */
    public function testWillCreateRoutesByMethod(
        string $groupPrefix,
        string $requestMethod,
        string $routePath,
        ?string $routeName = null,
        ?string $generatedPath = null,
        ?string $generatedName = null,
        ?array $allowedMethods = null,
        array|null|string $excludeMiddleware = [],
    ): void {
        $this->assertCount(0, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector
            ->setPrefix($groupPrefix)
            ->setMiddleware(MiddlewareInterface::class)
            ->$requestMethod(
                $routePath,
                RequestHandlerInterface::class,
                $routeName,
                $excludeMiddleware
            );
        $this->assertInstanceOf(RouteGroupCollector::class, $route);

        $this->assertCount(1, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertContainsOnlyInstancesOf(MiddlewareInterface::class, [$route->getMiddleware()]);
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
    }

    public static function methodProvider(): array
    {
        return [
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'any',
                'routePath'         => '/any',
                'routeName'         => null,
                'generatedPath'     => '/group/any',
                'generatedName'     => '/group/any',
                'allowedMethods'    => null,
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'any',
                'routePath'         => '/any',
                'routeName'         => null,
                'generatedPath'     => '/group/any',
                'generatedName'     => '/group/any',
                'allowedMethods'    => null,
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'any',
                'routePath'         => '/any',
                'routeName'         => 'app::group-any',
                'generatedPath'     => '/group/any',
                'generatedName'     => 'app::group-any',
                'allowedMethods'    => null,
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'any',
                'routePath'         => '/any',
                'routeName'         => 'app::group-any',
                'generatedPath'     => '/group/any',
                'generatedName'     => 'app::group-any',
                'allowedMethods'    => null,
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'delete',
                'routePath'         => '/delete',
                'routeName'         => null,
                'generatedPath'     => '/group/delete',
                'generatedName'     => '/group/delete^DELETE',
                'allowedMethods'    => ['DELETE'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'delete',
                'routePath'         => '/delete',
                'routeName'         => null,
                'generatedPath'     => '/group/delete',
                'generatedName'     => '/group/delete^DELETE',
                'allowedMethods'    => ['DELETE'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'delete',
                'routePath'         => '/delete',
                'routeName'         => 'app::group-delete',
                'generatedPath'     => '/group/delete',
                'generatedName'     => 'app::group-delete',
                'allowedMethods'    => ['DELETE'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'delete',
                'routePath'         => '/delete',
                'routeName'         => 'app::group-delete',
                'generatedPath'     => '/group/delete',
                'generatedName'     => 'app::group-delete',
                'allowedMethods'    => ['DELETE'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'get',
                'routePath'         => '/get',
                'routeName'         => null,
                'generatedPath'     => '/group/get',
                'generatedName'     => '/group/get^GET',
                'allowedMethods'    => ['GET'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'get',
                'routePath'         => '/get',
                'routeName'         => null,
                'generatedPath'     => '/group/get',
                'generatedName'     => '/group/get^GET',
                'allowedMethods'    => ['GET'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'get',
                'routePath'         => '/get',
                'routeName'         => 'app::group-get',
                'generatedPath'     => '/group/get',
                'generatedName'     => 'app::group-get',
                'allowedMethods'    => ['GET'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'get',
                'routePath'         => '/get',
                'routeName'         => 'app::group-get',
                'generatedPath'     => '/group/get',
                'generatedName'     => 'app::group-get',
                'allowedMethods'    => ['GET'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'patch',
                'routePath'         => '/patch',
                'routeName'         => null,
                'generatedPath'     => '/group/patch',
                'generatedName'     => '/group/patch^PATCH',
                'allowedMethods'    => ['PATCH'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'patch',
                'routePath'         => '/patch',
                'routeName'         => null,
                'generatedPath'     => '/group/patch',
                'generatedName'     => '/group/patch^PATCH',
                'allowedMethods'    => ['PATCH'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'patch',
                'routePath'         => '/patch',
                'routeName'         => 'app::group-patch',
                'generatedPath'     => '/group/patch',
                'generatedName'     => 'app::group-patch',
                'allowedMethods'    => ['PATCH'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'patch',
                'routePath'         => '/patch',
                'routeName'         => 'app::group-patch',
                'generatedPath'     => '/group/patch',
                'generatedName'     => 'app::group-patch',
                'allowedMethods'    => ['PATCH'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'post',
                'routePath'         => '/post',
                'routeName'         => null,
                'generatedPath'     => '/group/post',
                'generatedName'     => '/group/post^POST',
                'allowedMethods'    => ['POST'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'post',
                'routePath'         => '/post',
                'routeName'         => null,
                'generatedPath'     => '/group/post',
                'generatedName'     => '/group/post^POST',
                'allowedMethods'    => ['POST'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'post',
                'routePath'         => '/post',
                'routeName'         => 'app::group-post',
                'generatedPath'     => '/group/post',
                'generatedName'     => 'app::group-post',
                'allowedMethods'    => ['POST'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'post',
                'routePath'         => '/post',
                'routeName'         => 'app::group-post',
                'generatedPath'     => '/group/post',
                'generatedName'     => 'app::group-post',
                'allowedMethods'    => ['POST'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'put',
                'routePath'         => '/put',
                'routeName'         => null,
                'generatedPath'     => '/group/put',
                'generatedName'     => '/group/put^PUT',
                'allowedMethods'    => ['PUT'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'put',
                'routePath'         => '/put',
                'routeName'         => null,
                'generatedPath'     => '/group/put',
                'generatedName'     => '/group/put^PUT',
                'allowedMethods'    => ['PUT'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'put',
                'routePath'         => '/put',
                'routeName'         => 'app::group-put',
                'generatedPath'     => '/group/put',
                'generatedName'     => 'app::group-put',
                'allowedMethods'    => ['PUT'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'requestMethod'     => 'put',
                'routePath'         => '/put',
                'routeName'         => 'app::group-put',
                'generatedPath'     => '/group/put',
                'generatedName'     => 'app::group-put',
                'allowedMethods'    => ['PUT'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
        ];
    }

    public static function routeProvider(): array
    {
        return [
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => null,
                'generatedPath'     => '/group/route',
                'generatedName'     => '/group/route',
                'allowedMethods'    => null,
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => null,
                'generatedPath'     => '/group/route',
                'generatedName'     => '/group/route^DELETE:GET',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => null,
                'generatedPath'     => '/group/route',
                'generatedName'     => '/group/route^DELETE:GET',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => 'app::group-route',
                'generatedPath'     => '/group/route',
                'generatedName'     => 'app::group-route',
                'allowedMethods'    => null,
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => 'app::group-route',
                'generatedPath'     => '/group/route',
                'generatedName'     => 'app::group-route',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => [],
            ],
            [
                'groupPrefix'       => '/group',
                'routePath'         => '/route',
                'routeName'         => 'app::group-route',
                'generatedPath'     => '/group/route',
                'generatedName'     => 'app::group-route',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => [MiddlewareInterface::class],
            ],
        ];
    }
}
