# Upgrading from version 1 to version 2

Version 2 drops PHP 8.2, fixes how group middleware is applied, and makes the middleware container follow PSR-11.
Most applications only need to update PHP and run `composer update`; read the table below if your routes use groups with closures or `$excludeMiddleware`.

## Update the package

```shell
composer require dotkernel/dot-router:^2.0
```

## Changes that can break your code

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 support removed | Composer refuses to install version 2 | Use PHP 8.3 or newer, or stay on version 1 |
| `mezzio/mezzio-fastroute` minimum is `^3.15` | Composer reports a conflict with an older FastRoute version | Update `mezzio/mezzio-fastroute` |
| Callable route handlers in a group now run the group middleware | A closure route inside a group now runs, for example, the group's authentication middleware | This is usually what you want. To keep the old behaviour, list the group middleware in that route's `$excludeMiddleware` |
| `$excludeMiddleware` removes group middleware only | Listing the route's own handler or middleware class no longer removes it from the route | Remove the class from the route's middleware list instead |
| `$excludeMiddleware` matches middleware objects by class | A group middleware passed as an object is now excluded by its class name; excluding `MiddlewareInterface::class` removes every group middleware object, but never the route handler | Check routes that exclude interface names |
| `MiddlewareContainer::get()` throws `MissingMiddlewareException` or `InvalidMiddlewareException` | Code that compares the exception class with `Exception::class` no longer matches | Catch `Psr\Container\ContainerExceptionInterface`; both new classes extend `RuntimeException`, so `catch (Exception $e)` still works |
| An unregistered middleware class with required constructor arguments throws `InvalidMiddlewareException` | The error was an `ArgumentCountError` | Register the middleware in the container |
| `MiddlewareFactory::pipeline()` and `prepare([])` reject an empty list | `Dot\Router\Exception\InvalidArgumentException` when routes are registered; version 1 raised a PHP warning and registered a route that did nothing | Pass at least one middleware or handler |
| A group route with an empty prefix and an empty path is rejected | `Dot\Router\Exception\InvalidArgumentException` | Set a prefix or a path |
| Stricter PHPDoc types (`non-empty-string` paths and names, `list<string>` methods) | Static analysis of your code reports new errors on route calls | Pass non-empty literals, or narrow your variables before the call |

## Group middleware with closures

Before, the closure route skipped `AuthMiddleware`:

```php
$routeCollector->group('/admin', AuthMiddleware::class)
    ->get('/status', fn ($request, $handler) => new JsonResponse(['ok' => true]), 'admin:status');
```

After, `AuthMiddleware` runs first for `/admin/status`, as it does for every other route in the group.
To keep the route public, exclude the middleware:

```php
$routeCollector->group('/admin', AuthMiddleware::class)
    ->get('/status', fn ($request, $handler) => new JsonResponse(['ok' => true]), 'admin:status', AuthMiddleware::class);
```

## Excluding middleware objects

Before, a group middleware passed as an object could not be excluded by its class name:

```php
$routeCollector->group('/product', new CheckOwnerMiddleware($repository))
    ->post('/create', CreateProductHandler::class, 'product:create', CheckOwnerMiddleware::class);
// version 1: CheckOwnerMiddleware still ran
// version 2: CheckOwnerMiddleware is skipped for product:create
```
