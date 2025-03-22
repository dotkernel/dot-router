# Creating a single route

> Following this tutorial assumes that you already completed the [installation](../installation.md) and [configuration](../configuration.md) steps.

## Access the RouteCollector

Get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var \Dot\Router\RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(\Dot\Router\RouteCollectorInterface::class);
```

### Create a route that matches specific request methods

```php
$routeCollector->route('/resource/manage', SomeHandler::class, 'route-name', ['GET', 'POST']);
```

`route()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^METHOD1:METHOD2
- **methods**: optional, if omitted, the route will respond to all request methods

### Create a route that matches any request method

```php
$routeCollector->any('/resource/manage', SomeHandler::class, 'route-name');
```

`any()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set to the value of the **route path**

### Create a route that matches **DELETE** requests

```php
$routeCollector->delete('/resource/{id}', SomeHandler::class, 'route-name');
```

`delete()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^DELETE

### Create a route that matches **GET** requests

```php
$routeCollector->get('/resource/{id}', SomeHandler::class, 'route-name');
```

`get()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^GET

### Create a route that matches **PATCH** requests

```php
$routeCollector->patch('/resource/{id}', SomeHandler::class, 'route-name');
```

`patch()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PATCH

### Create a route that matches **POST** requests

```php
$routeCollector->post('/resource', SomeHandler::class, 'route-name');
```

`post()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^POST

### Create a route that matches **PUT** requests

```php
$routeCollector->put('/resource/{id}', SomeHandler::class, 'route-name');
```

`put()` method arguments:

- **path**: required, of type string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **name**: optional, if omitted, it will be set using the pattern **route path**^PUT

## Chain routes to the RouteCollector

In order to facilitate creating routes, each `$routeCollector` method returns the existing `RouteCollector` instance, so you can chain routes like this:

```php
$routeCollector
    ->route('/product', SomeHandler::class, 'app::product', ['GET', 'POST'])
    ->any('/category', SomeHandler::class, 'app::category')
    ->delete('/order/{id}', SomeHandler::class, 'app::delete-order')
    ->get('/order/{id}', SomeHandler::class, 'app::view-order')
    ->patch('/order/{id}', SomeHandler::class, 'app::update-order')
    ->post('/order', SomeHandler::class, 'app::create-order')
    ->put('/order/{id}', SomeHandler::class, 'app::replace-order');
```
