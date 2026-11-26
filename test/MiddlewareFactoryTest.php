<?php

declare(strict_types=1);

namespace DotTest\Router;

use Dot\Router\Exception\InvalidArgumentException;
use Dot\Router\Middleware\LazyLoadingMiddleware;
use DotTest\Router\Fixture\CallLog;
use DotTest\Router\Fixture\FirstMiddleware;
use DotTest\Router\Fixture\TestHandler;
use Laminas\Stratigility\Middleware\CallableMiddlewareDecorator;
use Laminas\Stratigility\Middleware\RequestHandlerMiddleware;
use Laminas\Stratigility\MiddlewarePipe;
use Mezzio\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewareFactoryTest extends AbstractRouterTestCase
{
    public function testPrepareReturnsMiddlewareUnchanged(): void
    {
        $middleware = new FirstMiddleware();

        $this->assertSame($middleware, $this->middlewareFactory->prepare($middleware));
    }

    public function testPrepareWrapsHandler(): void
    {
        $this->assertInstanceOf(
            RequestHandlerMiddleware::class,
            $this->middlewareFactory->prepare(new TestHandler())
        );
    }

    public function testPrepareDecoratesClosure(): void
    {
        $closure = static fn (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            => $handler->handle($request);

        $this->assertInstanceOf(CallableMiddlewareDecorator::class, $this->middlewareFactory->prepare($closure));
    }

    public function testPrepareDecoratesCallableArray(): void
    {
        $this->assertInstanceOf(
            CallableMiddlewareDecorator::class,
            $this->middlewareFactory->prepare([new TestHandler(), 'handle'])
        );
    }

    public function testPrepareMakesStringLazy(): void
    {
        $middleware = $this->middlewareFactory->prepare(FirstMiddleware::class);

        $this->assertInstanceOf(LazyLoadingMiddleware::class, $middleware);
        $this->assertSame(FirstMiddleware::class, $middleware->middlewareName);
        $this->assertSame([], CallLog::calls(), 'Lazy middleware must not run when prepared');
    }

    public function testPrepareBuildsPipelineFromList(): void
    {
        $middleware = $this->middlewareFactory->prepare([FirstMiddleware::class, TestHandler::class]);

        $this->assertInstanceOf(MiddlewarePipe::class, $middleware);
        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runMiddleware($middleware));
    }

    public function testPipelineAcceptsSeveralArguments(): void
    {
        $pipeline = $this->middlewareFactory->pipeline(FirstMiddleware::class, TestHandler::class);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runMiddleware($pipeline));
    }

    public function testPipelineAcceptsASingleList(): void
    {
        $pipeline = $this->middlewareFactory->pipeline([FirstMiddleware::class, TestHandler::class]);

        $this->assertSame([FirstMiddleware::class, TestHandler::class], $this->runMiddleware($pipeline));
    }

    public function testPipelineRejectsEmptyList(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->middlewareFactory->prepare([]);
    }

    public function testPipelineRejectsNoArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->middlewareFactory->pipeline();
    }

    /**
     * @return list<string>
     */
    private function runMiddleware(MiddlewareInterface $middleware): array
    {
        return $this->runRoute(new Route('/x', $middleware));
    }
}
