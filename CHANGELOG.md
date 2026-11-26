# Changelog

All notable changes to this project are documented in this file.

## 2.0.0 - TBD

### Added

- PHP 8.6 support.
- `Dot\Router\Exception\MissingMiddlewareException` (PSR-11 not-found), `InvalidMiddlewareException` (PSR-11 container exception) and `InvalidArgumentException`.
- Version 2 documentation and an upgrade guide.

### Changed

- Group middleware now also runs for callable route handlers (closures and callable arrays).
- `$excludeMiddleware` now removes group middleware only, never the route's own middleware or handler, and matches middleware objects by class.
- `MiddlewareContainer::get()` throws PSR-11 exceptions instead of `Exception` or `ArgumentCountError`.
- `MiddlewareFactory::pipeline()` and `prepare([])` throw `InvalidArgumentException` for an empty list.
- A group route whose prefix and path are both empty throws `InvalidArgumentException`.
- Minimum `mezzio/mezzio-fastroute` is `^3.15`; `mezzio/mezzio-router` and `psr/http-server-handler` are now declared requirements; minimum `psr/container` is `^1.1.2`.
- PHPDoc types narrowed for static analysis: `non-empty-string` paths and names, `list<string>` methods.
- PHPStan level 8, PHPUnit 12.

### Removed

- PHP 8.2 support.

### Fixed

- A `RouteGroupCollector` created by the container, or with `new`, no longer fails with an uninitialized `$middleware` property.
- Building a pipeline from an empty list no longer raises a PHP warning.

## 1.1.0 - 2025-10-30

### Added

- PHP 8.4 and 8.5 support (#11).

### Removed

- PHP 8.1 support (#11).

## 1.0.5 - 2025-03-22

### Changed

- Clearer instructions for creating single routes and route groups (#9).

## 1.0.4 - 2025-03-20

### Changed

- Group middleware is merged with the route handlers (#7).

## 1.0.3 - 2025-03-20

### Changed

- The middleware argument of `RouteCollector::group()` is optional (#5).

## 1.0.2 - 2025-03-20

### Changed

- Allow `laminas/laminas-stratigility` 3.x and 4.x (#3).

## 1.0.1 - 2025-03-20

### Changed

- Documentation tweaks.

## 1.0.0 - 2025-03-20

### Added

- Initial release.
