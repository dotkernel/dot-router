<?php

declare(strict_types=1);

namespace DotTest\Router\Middleware;

use Dot\Router\Middleware\LazyLoadingMiddleware;
use Dot\Router\MiddlewareContainer;
use DotTest\Router\AbstractRouterTestCase;
use DotTest\Router\Fixture\FallbackHandler;
use DotTest\Router\Fixture\FirstMiddleware;
use DotTest\Router\Fixture\MiddlewareWithArgument;
use Mezzio\Router\Route;

final class LazyLoadingMiddlewareTest extends AbstractRouterTestCase
{
    public function testResolvesMiddlewareOnlyWhenProcessing(): void
    {
        $middleware = new LazyLoadingMiddleware(new MiddlewareContainer($this->container), 'lazy');
        $this->container->set('lazy', new MiddlewareWithArgument('resolved'));

        $this->assertSame(['resolved', FallbackHandler::class], $this->runRoute(new Route('/x', $middleware)));
    }

    public function testExposesMiddlewareName(): void
    {
        $middleware = new LazyLoadingMiddleware(new MiddlewareContainer($this->container), FirstMiddleware::class);

        $this->assertSame(FirstMiddleware::class, $middleware->middlewareName);
    }
}
