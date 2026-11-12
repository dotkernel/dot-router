<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\MiddlewareContainer;
use Dot\Router\MiddlewareFactory;
use DotTest\Router\Fixture\CallLog;
use DotTest\Router\Fixture\FallbackHandler;
use DotTest\Router\Fixture\InMemoryContainer;
use Mezzio\Router\Route;
use Mezzio\Router\RouteCollector as MezzioRouteCollector;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function sprintf;

/**
 * Builds real collaborators and runs a route's middleware the way an application would.
 */
abstract class AbstractRouterTestCase extends TestCase
{
    protected InMemoryContainer $container;
    protected MiddlewareFactory $middlewareFactory;
    protected MezzioRouteCollector $mezzioCollector;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->container         = new InMemoryContainer();
        $this->middlewareFactory = new MiddlewareFactory(new MiddlewareContainer($this->container));
        $this->mezzioCollector   = new MezzioRouteCollector($this->createStub(RouterInterface::class));
        $this->request           = $this->createStub(ServerRequestInterface::class);
        CallLog::reset($this->createStub(ResponseInterface::class));
    }

    /**
     * @return list<string>
     */
    protected function runRoute(Route $route): array
    {
        $route->getMiddleware()->process($this->request, new FallbackHandler());

        return CallLog::calls();
    }

    protected function routeNamed(string $name): Route
    {
        foreach ($this->mezzioCollector->getRoutes() as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        $this->fail(sprintf('Route "%s" was not registered', $name));
    }
}
