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

In order to use this feature, first get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(RouteCollectorInterface::class);
```

Then rewrite the above routes using the below code:

```php
$routeCollector->group('/product')
    ->post('/create', CreateProductHandler::class, 'product:create');
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete');
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update');
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

Note that **/product** becomes the group prefix and the routes only specify part that is specific only to them.
When `dot-router` registers the routes, it will automatically prepend the prefix to each route path.

### Advantages

- DRY: no need for repeating common route parts
- encapsulation: similar routes are grouped in a single block of code (vs each route a separate statement)
- easy path refactoring: modify all routes at once by changing only the prefix
- easy copying/moving: copying/moving an entire group makes sure that you don't accidentally omit a route

## Exclude middleware

Besides sharing the same path, sometimes routes may need to share handlers/middlewares too.
A good example is the usage presence of `CheckOwnerMiddleware` in the below example:

```php
$app->post('/product/create', CreateProductHandler::class, 'product:create');
$app->delete('/product/delete/{id}', [CheckOwnerMiddleware::class, DeleteProductHandler::class], 'product:delete');
$app->patch('/product/update/{id}', [CheckOwnerMiddleware::class, UpdateProductHandler::class], 'product:update');
$app->get('/product/view/{id}', [CheckOwnerMiddleware::class, GetProductHandler::class], 'product:view');
```

Just like in the first example, the routes are similar but this time there is `CheckOwnerMiddleware`, a middleware used by three out of the four routes.

For such cases, `dot-router` provides for specific routes the ability to exclude a middleware from their pipeline.

Using this feature, we can rewrite the above example like this:

```php
$routeCollector->group('/product', CheckOwnerMiddleware::class)
    ->post('/create', CreateProductHandler::class, 'product:create', CheckOwnerMiddleware::class)
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

Note the second argument of the `group` method is `CheckOwnerMiddleware::class`.
This way all group middlewares will be prepended to the route's handler, so this is the equivalent of using:

```php
$app->post('/product/create', [CheckOwnerMiddleware::class, CreateProductHandler::class], 'product:create')
```

### Advantages

- extends the DRY feature of route groups by allowing to specify common middlewares only once per route
- shorter and easier-to-read route definitions
