# Features

## Route groups

During development, developers often need to create sets of visually similar routes.
The traditional way is creating each route, one by one, like in the below example:

```php
$app->post('/product/create', CreateProductHandler::class, 'product:create');
$app->delete('/product/delete/{id}', DeleteProductHandler::class, 'product:delete');
$app->patch('/product/update/{id}', UpdateProductHandler::class, 'product:update');
$app->get('/product/view/{id}', GetProductHandler::class, 'product:view');
```

Besides the features provided by `mezzio/mezzio-fastroute`, `dot-router` offers route groups, which are collections of routes that (partially) share the same path.

To use this feature, first get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var \Dot\Router\RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(\Dot\Router\RouteCollectorInterface::class);
```

Then rewrite the above routes using the below code:

```php
$routeCollector->group('/product')
    ->post('/create', CreateProductHandler::class, 'product:create')
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

Note that **/product** becomes the group prefix and the routes only specify the part that is specific to them.
When `dot-router` registers the routes, it prepends the prefix to each route path.
The prefix and the path are joined as they are, so do not end the prefix with `/` (`/product/` plus `/create` gives `/product//create`).

### Advantages

- DRY: no need for repeating common route parts
- encapsulation: similar routes are grouped in a single block of code (vs. each route a separate statement)
- easy path refactoring: modify all routes at once by changing only the prefix
- easy copying/moving: copying/moving an entire group makes sure that you don't accidentally omit a route

## Group middleware

Besides sharing the same path, routes often share middleware too.
A good example is `CheckOwnerMiddleware` in the below example:

```php
$app->delete('/product/delete/{id}', [CheckOwnerMiddleware::class, DeleteProductHandler::class], 'product:delete');
$app->patch('/product/update/{id}', [CheckOwnerMiddleware::class, UpdateProductHandler::class], 'product:update');
$app->get('/product/view/{id}', [CheckOwnerMiddleware::class, GetProductHandler::class], 'product:view');
```

Pass the shared middleware as the second argument of `group()`, and `dot-router` runs it before the handler of every route in the group:

```php
$routeCollector->group('/product', CheckOwnerMiddleware::class)
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

Group middleware runs first, in the order given, followed by the route's own middleware and handler.
This applies to every kind of route handler: class names, objects, closures and other callables.

## Exclude middleware

Sometimes one route in a group must not run a group middleware.
In the below example, creating a product has no owner to check yet:

```php
$app->post('/product/create', CreateProductHandler::class, 'product:create');
$app->delete('/product/delete/{id}', [CheckOwnerMiddleware::class, DeleteProductHandler::class], 'product:delete');
$app->patch('/product/update/{id}', [CheckOwnerMiddleware::class, UpdateProductHandler::class], 'product:update');
$app->get('/product/view/{id}', [CheckOwnerMiddleware::class, GetProductHandler::class], 'product:view');
```

For such cases, each group route accepts `$excludeMiddleware` as its last argument:

```php
$routeCollector->group('/product', CheckOwnerMiddleware::class)
    ->post('/create', CreateProductHandler::class, 'product:create', CheckOwnerMiddleware::class)
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

The `product:create` route is now the equivalent of:

```php
$app->post('/product/create', CreateProductHandler::class, 'product:create');
```

How exclusion works:

- `$excludeMiddleware` is a class name or a list of class names.
- It removes **group** middleware only. The route's own middleware and handler always run.
- A group middleware given as a class name is excluded when that exact name is listed.
- A group middleware given as an object is excluded when it is an instance of a listed class or interface.

### Advantages

- extends the DRYness of route groups by allowing to specify common middlewares only once per group
- shorter and easier-to-read route definitions
