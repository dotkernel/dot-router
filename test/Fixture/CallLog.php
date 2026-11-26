<?php

declare(strict_types=1);

namespace DotTest\Router\Fixture;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Records which middleware and handlers ran, in order.
 *
 * Static because lazy-loaded fixtures are created with "new $class()" and take no arguments.
 */
final class CallLog
{
    /** @var list<string> */
    private static array $calls = [];

    private static ?ResponseInterface $response = null;

    public static function reset(ResponseInterface $response): void
    {
        self::$calls    = [];
        self::$response = $response;
    }

    public static function record(string $name): void
    {
        self::$calls[] = $name;
    }

    /**
     * @return list<string>
     */
    public static function calls(): array
    {
        return self::$calls;
    }

    public static function response(): ResponseInterface
    {
        if (self::$response === null) {
            throw new RuntimeException('Call CallLog::reset() in setUp() first');
        }

        return self::$response;
    }
}
