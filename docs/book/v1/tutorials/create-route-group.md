# Creating route groups

> Following this tutorial assumes that you already completed the [installation](../installation.md) and [configuration](../configuration.md) steps.

Get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var \Dot\Router\RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(\Dot\Router\RouteCollectorInterface::class);
```

Create a route group, by setting the route group prefix and optionally a(n array of) common middleware(s):

```php
$routeCollector->group('/product', SomeMiddleware::class)
```

Then start appending routes to the route group by chaining them:

```php
$routeCollector->group('/product', CheckOwnerMiddleware::class)
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```

If some routes don't need to be piped through specific middleware(s), you can exclude it/them at route level:

```php
$routeCollector->group('/product', CheckOwnerMiddleware::class)
    ->post('/create', CreateProductHandler::class, 'product:create', CheckOwnerMiddleware::class)
    ->delete('/delete/{id}', DeleteProductHandler::class, 'product:delete')
    ->patch('/update/{id}', UpdateProductHandler::class, 'product:update')
    ->get('/view/{id}', GetProductHandler::class, 'product:view');
```
