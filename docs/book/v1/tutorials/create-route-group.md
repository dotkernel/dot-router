# Creating route groups

> Following this tutorial assumes that you already completed the [installation](../installation.md) and [configuration](../configuration.md) steps.

## Access the RouteCollector

Get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var \Dot\Router\RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(\Dot\Router\RouteCollectorInterface::class);
```

### Create route group

Create a route group, by setting the route group prefix and optionally a(n array of) common middleware(s):

```php
$routeCollector->group('/resource', SomeMiddleware::class);
```

`group` method arguments:

- **prefix**: required, a string that will be prepended to each route from the group
- **middleware**: optional, (array of) request middleware(s) to execute before the route handler(s)

### Append a route that matches specific request methods

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->route('/manage', ResourceHandler::class, 'route-name', ['GET', 'POST']);
```

If this route does not need to be piped through specific middleware(s), you can specify the fifth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->route('/manage', ResourceHandler::class, 'route-name', ['GET', 'POST'], excludeMiddleware: SomeMiddleware::class);
```

`route()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^METHOD1:METHOD2
- **methods**: optional, if omitted, the route will respond to all request methods (equivalent of `any()`)
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches any request method

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->any('/manage', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->any('/manage', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`any()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set to the value of the **route path**
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches **DELETE** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->delete('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->delete('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`delete()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^DELETE
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches **GET** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->get('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->get('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`get()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^GET
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches **PATCH** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->patch('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->patch('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`patch()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PATCH
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches **POST** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->post('', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->post('', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`post()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^POST
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route

### Append a route that matches **PUT** requests

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->put('/{id}', ResourceHandler::class, 'route-name');
```

If this route does not need to be piped through specific middleware(s), you can specify the fourth argument `$excludeMiddleware` exclude those middlewares at route level (makes sense when there are multiple routes in a group):

```php
$routeCollector->group('/resource', [SomeMiddleware::class, OtherMiddleware::class])
    ->put('/{id}', ResourceHandler::class, 'route-name', excludeMiddleware: SomeMiddleware::class);
```

`put()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PUT
- **excludeMiddleware**: optional, (array of) request handler(s)/middleware(s) to exclude for this route
