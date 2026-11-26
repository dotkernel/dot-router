<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\RouteCollector;
use Dot\Router\RouteGroupCollector;
use DotTest\Router\Fixture\FirstMiddleware;
use DotTest\Router\Fixture\SecondMiddleware;
use DotTest\Router\Fixture\TestHandler;
use PHPUnit\Framework\Attributes\DataProvider;

use function strtoupper;

final class RouteCollectorTest extends AbstractRouterTestCase
{
    private RouteCollector $routeCollector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeCollector = new RouteCollector($this->mezzioCollector, $this->middlewareFactory);
    }

    public function testWillCreateGroup(): void
    {
        $group = $this->routeCollector->group('/group', FirstMiddleware::class);

        $this->assertInstanceOf(RouteGroupCollector::class, $group);
        $this->assertSame('/group', $group->getPrefix());
        $this->assertSame(FirstMiddleware::class, $group->getMiddleware());
    }

    public function testGroupMiddlewareIsOptional(): void
    {
        $group = $this->routeCollector->group('/group');

        $this->assertNull($group->getMiddleware());

        $group->get('/x', TestHandler::class, 'x');
        $this->assertSame([TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testEachGroupIsANewInstance(): void
    {
        $first  = $this->routeCollector->group('/first');
        $second = $this->routeCollector->group('/second');

        $this->assertNotSame($first, $second);
        $this->assertSame('/first', $first->getPrefix());
    }

    public function testRouteRunsMiddlewareInOrder(): void
    {
        $this->routeCollector->get(
            '/x',
            [FirstMiddleware::class, new SecondMiddleware(), TestHandler::class],
            'x'
        );

        $this->assertSame(
            [FirstMiddleware::class, SecondMiddleware::class, TestHandler::class],
            $this->runRoute($this->routeNamed('x'))
        );
    }

    /**
     * @param non-empty-string $routePath
     * @param non-empty-string|null $routeName
     * @param list<string>|null $allowedMethods
     */
    #[DataProvider('routeProvider')]
    public function testWillCreateRoute(
        string $routePath,
        ?string $routeName,
        string $generatedPath,
        string $generatedName,
        ?array $allowedMethods,
    ): void {
        $this->assertCount(0, $this->routeCollector->getRoutes());

        $result = $this->routeCollector->route($routePath, TestHandler::class, $routeName, $allowedMethods);
        $this->assertSame($this->routeCollector, $result);
        $this->assertCount(1, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
        $this->assertSame([TestHandler::class], $this->runRoute($route));
    }

    /**
     * @param 'any'|'delete'|'get'|'patch'|'post'|'put' $requestMethod
     * @param non-empty-string $routePath
     * @param non-empty-string|null $routeName
     * @param list<string>|null $allowedMethods
     */
    #[DataProvider('methodProvider')]
    public function testWillCreateRoutesByMethod(
        string $requestMethod,
        string $routePath,
        ?string $routeName,
        string $generatedPath,
        string $generatedName,
        ?array $allowedMethods,
    ): void {
        $this->assertCount(0, $this->routeCollector->getRoutes());

        $result = $this->routeCollector->$requestMethod($routePath, TestHandler::class, $routeName);
        $this->assertSame($this->routeCollector, $result);
        $this->assertCount(1, $this->routeCollector->getRoutes());

        $route = $this->routeCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
        $this->assertSame([TestHandler::class], $this->runRoute($route));
    }

    /**
     * @return array<string, array{
     *     requestMethod: 'any'|'delete'|'get'|'patch'|'post'|'put',
     *     routePath: non-empty-string,
     *     routeName: non-empty-string|null,
     *     generatedPath: string,
     *     generatedName: string,
     *     allowedMethods: list<string>|null,
     * }>
     */
    public static function methodProvider(): array
    {
        $cases = [];
        foreach (['any', 'delete', 'get', 'patch', 'post', 'put'] as $method) {
            $path    = '/' . $method;
            $methods = $method === 'any' ? null : [strtoupper($method)];
            $default = $methods === null ? $path : $path . '^' . strtoupper($method);

            $cases[$method . ' without name'] = [
                'requestMethod'  => $method,
                'routePath'      => $path,
                'routeName'      => null,
                'generatedPath'  => $path,
                'generatedName'  => $default,
                'allowedMethods' => $methods,
            ];
            $cases[$method . ' with name']    = [
                'requestMethod'  => $method,
                'routePath'      => $path,
                'routeName'      => 'app::' . $method,
                'generatedPath'  => $path,
                'generatedName'  => 'app::' . $method,
                'allowedMethods' => $methods,
            ];
        }

        return $cases;
    }

    /**
     * @return array<string, array{
     *     routePath: non-empty-string,
     *     routeName: non-empty-string|null,
     *     generatedPath: string,
     *     generatedName: string,
     *     allowedMethods: list<string>|null,
     * }>
     */
    public static function routeProvider(): array
    {
        return [
            'any method, no name'    => [
                'routePath'      => '/route',
                'routeName'      => null,
                'generatedPath'  => '/route',
                'generatedName'  => '/route',
                'allowedMethods' => null,
            ],
            'two methods, no name'   => [
                'routePath'      => '/route',
                'routeName'      => null,
                'generatedPath'  => '/route',
                'generatedName'  => '/route^DELETE:GET',
                'allowedMethods' => ['DELETE', 'GET'],
            ],
            'any method, with name'  => [
                'routePath'      => '/route',
                'routeName'      => 'app::name',
                'generatedPath'  => '/route',
                'generatedName'  => 'app::name',
                'allowedMethods' => null,
            ],
            'two methods, with name' => [
                'routePath'      => '/route',
                'routeName'      => 'app::name',
                'generatedPath'  => '/route',
                'generatedName'  => 'app::name',
                'allowedMethods' => ['DELETE', 'GET'],
            ],
        ];
    }
}
