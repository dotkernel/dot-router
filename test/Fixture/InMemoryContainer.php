<?php

declare(strict_types=1);

namespace DotTest\Router\Fixture;

use Psr\Container\ContainerInterface;

use function array_key_exists;

final class InMemoryContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(
        private array $services = [],
    ) {
    }

    public function set(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->services)) {
            throw ServiceNotFoundException::forService($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
