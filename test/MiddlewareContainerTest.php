<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\Exception\InvalidMiddlewareException;
use Dot\Router\Exception\MissingMiddlewareException;
use Dot\Router\MiddlewareContainer;
use DotTest\Router\Fixture\FirstMiddleware;
use DotTest\Router\Fixture\InMemoryContainer;
use DotTest\Router\Fixture\MiddlewareWithArgument;
use DotTest\Router\Fixture\TestHandler;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

final class MiddlewareContainerTest extends TestCase
{
    private InMemoryContainer $services;
    private MiddlewareContainer $container;

    protected function setUp(): void
    {
        $this->services  = new InMemoryContainer();
        $this->container = new MiddlewareContainer($this->services);
    }

    public function testHasRegisteredService(): void
    {
        $this->services->set('my-middleware', new FirstMiddleware());

        $this->assertTrue($this->container->has('my-middleware'));
    }

    public function testHasAutoloadableClass(): void
    {
        $this->assertTrue($this->container->has(FirstMiddleware::class));
    }

    public function testDoesNotHaveUnknownId(): void
    {
        $this->assertFalse($this->container->has('Missing\\Middleware'));
    }

    public function testGetReturnsRegisteredService(): void
    {
        $middleware = new MiddlewareWithArgument('registered');
        $this->services->set(MiddlewareWithArgument::class, $middleware);

        $this->assertSame($middleware, $this->container->get(MiddlewareWithArgument::class));
    }

    public function testGetInstantiatesUnregisteredClass(): void
    {
        $this->assertInstanceOf(FirstMiddleware::class, $this->container->get(FirstMiddleware::class));
    }

    public function testGetWrapsHandler(): void
    {
        $this->assertInstanceOf(RequestHandlerMiddleware::class, $this->container->get(TestHandler::class));
    }

    public function testGetThrowsNotFoundForUnknownService(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->expectException(MissingMiddlewareException::class);

        $this->container->get('Missing\\Middleware');
    }

    public function testGetThrowsContainerExceptionForNonMiddleware(): void
    {
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectException(InvalidMiddlewareException::class);
        $this->expectExceptionMessage('resolved to "stdClass"');

        $this->container->get(stdClass::class);
    }

    public function testGetThrowsContainerExceptionForClassWithRequiredArguments(): void
    {
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectException(InvalidMiddlewareException::class);
        $this->expectExceptionMessage('register it in the container');

        $this->container->get(MiddlewareWithArgument::class);
    }
}
