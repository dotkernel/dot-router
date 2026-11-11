<?php

declare(strict_types=1);

namespace DotTest\Router\Fixture;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Stands in for the next handler in the application pipeline. If it runs, the route fell through.
 */
final class FallbackHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        CallLog::record(self::class);

        return CallLog::response();
    }
}
