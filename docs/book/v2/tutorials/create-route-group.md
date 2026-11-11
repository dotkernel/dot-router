# Creating route groups

> Following this tutorial assumes that you already completed the [installation](../installation.md) and [configuration](../configuration.md) steps.

## Access the RouteCollector

Get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var \Dot\Router\RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(\Dot\Router\RouteCollectorInterface::class);
```

### Create a route group

Create a route group by setting the route group prefix and optionally a(n array of) common middleware(s):

```php
$routeCollector->group('/resource', SomeMiddleware::class);
```

`group` method arguments:

- **prefix**: required, a string that will be prepended to each route from the group; do not end it with `/`
- **middleware**: optional, (array of) request middleware(s) to execute before the handler of every route in the group, including closures and other callables

Each call to `group()` returns a new group.
Create groups this way rather than fetching `Dot\Router\RouteGroupCollectorInterface` from the container: the container returns the same shared instance every time, so its prefix and middleware would leak between modules.

### Which middleware is excluded

`$excludeMiddleware` removes group middleware only; the route's own middleware and handler always run.
A group middleware given as a class name is excluded when that exact name is listed.
A group middleware given as an object is excluded when it is an instance of a listed class or interface.

### Append a route that matches specific request methods

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->route('/manage', ResourceHandler::class, 'route-name', ['GET', 'POST']);
```

If this route does not need to be piped through specific middleware(s), you can use the fifth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->route('/manage', ResourceHandler::class, 'route-name', ['GET', 'POST'], excludeMiddleware: SomeMiddleware::class);
```

`route()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: (optional) if omitted, it will be set using the pattern **route path**^METHOD1:METHOD2
- **methods**: (optional) if omitted, the route will respond to all request methods (equivalent of `any()`)
- **excludeMiddleware**: (optional) class name, or list of class names, of group middleware to skip for this route

### Append a route that matches any request method

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->any('/manage', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->any('/manage', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`any()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set to the value of the **route path**
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

### Append a route that matches **DELETE** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->delete('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->delete('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`delete()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^DELETE
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

### Append a route that matches **GET** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->get('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->get('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`get()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^GET
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

### Append a route that matches **PATCH** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->patch('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->patch('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`patch()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PATCH
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

### Append a route that matches **POST** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->post('', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->post('', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`post()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^POST
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

### Append a route that matches **PUT** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->put('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can use the fourth argument, `$excludeMiddleware`, to exclude those group middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->put('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`put()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PUT
- **excludeMiddleware**: optional, class name, or list of class names, of group middleware to skip for this route

## List the registered routes

`getRoutes()` on a group returns every route registered in the application's route collector, not only the routes of that group.
