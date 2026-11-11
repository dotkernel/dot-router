<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\Exception\InvalidArgumentException;
use Dot\Router\RouteGroupCollector;
use DotTest\Router\Fixture\CallLog;
use DotTest\Router\Fixture\FallbackHandler;
use DotTest\Router\Fixture\FirstMiddleware;
use DotTest\Router\Fixture\MiddlewareWithArgument;
use DotTest\Router\Fixture\SecondMiddleware;
use DotTest\Router\Fixture\TestHandler;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function strtoupper;

final class RouteGroupCollectorTest extends AbstractRouterTestCase
{
    private RouteGroupCollector $routeGroupCollector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->routeGroupCollector = new RouteGroupCollector($this->mezzioCollector, $this->middlewareFactory);
    }

    public function testWillCreateGroup(): void
    {
        $group = $this->routeGroupCollector->setPrefix('/group')->setMiddleware(FirstMiddleware::class);

        $this->assertSame('/group', $group->getPrefix());
        $this->assertSame(FirstMiddleware::class, $group->getMiddleware());
    }

    public function testMiddlewareIsNullByDefault(): void
    {
        $this->assertNull($this->routeGroupCollector->getMiddleware());
    }

    public function testGroupWithoutSetMiddlewareCanAddRoutes(): void
    {
        $this->routeGroupCollector->setPrefix('/group')->get('/x', TestHandler::class, 'x');

        $this->assertSame([TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testGroupMiddlewareRunsBeforeRouteMiddleware(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware([FirstMiddleware::class, new SecondMiddleware()])
            ->get('/x', TestHandler::class, 'x');

        $this->assertSame(
            [FirstMiddleware::class, SecondMiddleware::class, TestHandler::class],
            $this->runRoute($this->routeNamed('x'))
        );
    }

    public function testCallableRouteRunsGroupMiddleware(): void
    {
        $closure = static function (
            ServerRequestInterface $request,
            RequestHandlerInterface $handler,
        ): ResponseInterface {
            CallLog::record('closure');

            return CallLog::response();
        };

        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->get('/x', $closure, 'x');

        $this->assertSame([FirstMiddleware::class, 'closure'], $this->runRoute($this->routeNamed('x')));
    }

    public function testCallableArrayRouteRunsGroupMiddleware(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->get('/x', [new TestHandler(), 'handle'], 'x');

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testExcludeRemovesGroupMiddlewareByClassName(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware([FirstMiddleware::class, SecondMiddleware::class])
            ->get('/x', TestHandler::class, 'x', FirstMiddleware::class);

        $this->assertSame([SecondMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testExcludeAcceptsAList(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware([FirstMiddleware::class, SecondMiddleware::class])
            ->get('/x', TestHandler::class, 'x', [FirstMiddleware::class, SecondMiddleware::class]);

        $this->assertSame([TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testExcludeRemovesGroupMiddlewareInstanceByClass(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware([new MiddlewareWithArgument('instance'), FirstMiddleware::class])
            ->get('/x', TestHandler::class, 'x', MiddlewareWithArgument::class);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testExcludeByInterfaceRemovesOnlyGroupMiddlewareObjects(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware([new MiddlewareWithArgument('instance'), FirstMiddleware::class])
            ->get('/x', new TestHandler(), 'x', MiddlewareInterface::class);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testExcludeNeverRemovesRouteHandler(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->get('/x', new TestHandler(), 'x', RequestHandlerInterface::class)
            ->get('/y', TestHandler::class, 'y', TestHandler::class);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
        $this->assertNotContains(FallbackHandler::class, CallLog::calls());
    }

    public function testExcludeDoesNotRemoveTheSameMiddlewareFromTheRoute(): void
    {
        $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->get('/x', [FirstMiddleware::class, TestHandler::class], 'x', FirstMiddleware::class);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runRoute($this->routeNamed('x')));
    }

    public function testEmptyPrefixAndPathIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->routeGroupCollector->get('', TestHandler::class);
    }

    public function testEmptyRoutePathUsesThePrefix(): void
    {
        $this->routeGroupCollector->setPrefix('/group')->post('', TestHandler::class, 'x');

        $this->assertSame('/group', $this->routeNamed('x')->getPath());
    }

    public function testGetRoutesReturnsEveryRouteInTheCollector(): void
    {
        $this->mezzioCollector->get('/home', $this->middlewareFactory->prepare(TestHandler::class), 'home');
        $this->routeGroupCollector->setPrefix('/group')->get('/x', TestHandler::class, 'x');

        $this->assertCount(2, $this->routeGroupCollector->getRoutes());
    }

    /**
     * @param non-empty-string|null $routeName
     * @param list<string>|null $allowedMethods
     * @param class-string|list<class-string> $excludeMiddleware
     */
    #[DataProvider('routeProvider')]
    public function testWillCreateRoute(
        ?string $routeName,
        string $generatedName,
        ?array $allowedMethods,
        array|string $excludeMiddleware,
    ): void {
        $this->assertCount(0, $this->routeGroupCollector->getRoutes());

        $result = $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->route('/route', TestHandler::class, $routeName, $allowedMethods, $excludeMiddleware);
        $this->assertSame($this->routeGroupCollector, $result);
        $this->assertCount(1, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector->getRoutes()[0];
        $this->assertSame('/group/route', $route->getPath());
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
        $this->assertSame($this->expectedCalls($excludeMiddleware), $this->runRoute($route));
    }

    /**
     * @param 'any'|'delete'|'get'|'patch'|'post'|'put' $requestMethod
     * @param non-empty-string|null $routeName
     * @param list<string>|null $allowedMethods
     * @param class-string|list<class-string> $excludeMiddleware
     */
    #[DataProvider('methodProvider')]
    public function testWillCreateRoutesByMethod(
        string $requestMethod,
        ?string $routeName,
        string $generatedPath,
        string $generatedName,
        ?array $allowedMethods,
        array|string $excludeMiddleware,
    ): void {
        $this->assertCount(0, $this->routeGroupCollector->getRoutes());

        $result = $this->routeGroupCollector
            ->setPrefix('/group')
            ->setMiddleware(FirstMiddleware::class)
            ->$requestMethod('/' . $requestMethod, TestHandler::class, $routeName, $excludeMiddleware);
        $this->assertSame($this->routeGroupCollector, $result);
        $this->assertCount(1, $this->routeGroupCollector->getRoutes());

        $route = $this->routeGroupCollector->getRoutes()[0];
        $this->assertSame($generatedPath, $route->getPath());
        $this->assertSame($generatedName, $route->getName());
        $this->assertSame($allowedMethods, $route->getAllowedMethods());
        $this->assertSame($this->expectedCalls($excludeMiddleware), $this->runRoute($route));
    }

    /**
     * @return array<string, array{
     *     requestMethod: 'any'|'delete'|'get'|'patch'|'post'|'put',
     *     routeName: non-empty-string|null,
     *     generatedPath: string,
     *     generatedName: string,
     *     allowedMethods: list<string>|null,
     *     excludeMiddleware: class-string|list<class-string>,
     * }>
     */
    public static function methodProvider(): array
    {
        $cases = [];
        foreach (['any', 'delete', 'get', 'patch', 'post', 'put'] as $method) {
            $path    = '/group/' . $method;
            $methods = $method === 'any' ? null : [strtoupper($method)];
            $default = $methods === null ? $path : $path . '^' . strtoupper($method);

            foreach (self::excludeCases() as $excludeLabel => $exclude) {
                $cases[$method . ', no name, ' . $excludeLabel]   = [
                    'requestMethod'     => $method,
                    'routeName'         => null,
                    'generatedPath'     => $path,
                    'generatedName'     => $default,
                    'allowedMethods'    => $methods,
                    'excludeMiddleware' => $exclude,
                ];
                $cases[$method . ', with name, ' . $excludeLabel] = [
                    'requestMethod'     => $method,
                    'routeName'         => 'app::group-' . $method,
                    'generatedPath'     => $path,
                    'generatedName'     => 'app::group-' . $method,
                    'allowedMethods'    => $methods,
                    'excludeMiddleware' => $exclude,
                ];
            }
        }

        return $cases;
    }

    /**
     * @return array<string, array{
     *     routeName: non-empty-string|null,
     *     generatedName: string,
     *     allowedMethods: list<string>|null,
     *     excludeMiddleware: class-string|list<class-string>,
     * }>
     */
    public static function routeProvider(): array
    {
        $cases = [];
        foreach (self::excludeCases() as $excludeLabel => $exclude) {
            $cases['any method, no name, ' . $excludeLabel]    = [
                'routeName'         => null,
                'generatedName'     => '/group/route',
                'allowedMethods'    => null,
                'excludeMiddleware' => $exclude,
            ];
            $cases['two methods, no name, ' . $excludeLabel]   = [
                'routeName'         => null,
                'generatedName'     => '/group/route^DELETE:GET',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => $exclude,
            ];
            $cases['two methods, with name, ' . $excludeLabel] = [
                'routeName'         => 'app::group-route',
                'generatedName'     => 'app::group-route',
                'allowedMethods'    => ['DELETE', 'GET'],
                'excludeMiddleware' => $exclude,
            ];
        }

        return $cases;
    }

    /**
     * @return array<string, class-string|list<class-string>>
     */
    private static function excludeCases(): array
    {
        return [
            'no exclusion'           => [],
            'exclude by string'      => FirstMiddleware::class,
            'exclude by list'        => [FirstMiddleware::class],
            'exclude unrelated name' => [SecondMiddleware::class],
        ];
    }

    /**
     * @param class-string|list<class-string> $excludeMiddleware
     * @return list<string>
     */
    private function expectedCalls(array|string $excludeMiddleware): array
    {
        $excluded = $excludeMiddleware === FirstMiddleware::class || $excludeMiddleware === [FirstMiddleware::class];

        return $excluded ? [TestHandler::class] : [FirstMiddleware::class, TestHandler::class];
    }
}
