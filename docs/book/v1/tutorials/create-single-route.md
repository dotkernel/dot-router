# Creating a single route

> Following this tutorial assumes that you already completed the [installation](../installation.md) and [configuration](../configuration.md) steps.

Get an instance of the `Dot\Router\RouteCollector` using the below code:

```php
/** @var RouteCollectorInterface $routeCollector */
$routeCollector = $container->get(RouteCollectorInterface::class);
```

## Create a route that responds to any request method

```php
$routeCollector->any('/path', SomeHandler::class, 'route-name');
```

`any()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set to the value of the **route path**

## Create a route that responds to specific request methods

```php
$routeCollector->route('/path', SomeHandler::class, 'route-name', ['GET', 'POST']);
```

`route()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^METHOD1:METHOD2
- **allowed methods**: optional, if omitted, the route will respond to all request methods (equivalent of `any()`)

## Create a route that responds to **DELETE** requests

```php
$routeCollector->delete('/path', SomeHandler::class, 'route-name');
```

`delete()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^DELETE

## Create a route that responds to **GET** requests

```php
$routeCollector->get('/path', SomeHandler::class, 'route-name');
```

`get()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^GET

## Create a route that responds to **PATCH** requests

```php
$routeCollector->patch('/path', SomeHandler::class, 'route-name');
```

`patch()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^PATCH

## Create a route that responds to **POST** requests

```php
$routeCollector->post('/path', SomeHandler::class, 'route-name');
```

`post()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^POST

## Create a route that responds to **PUT** requests

```php
$routeCollector->put('/path', SomeHandler::class, 'route-name');
```

`put()` method arguments:

- **route path**: required, non-empty string
- **middleware**: required, (array of) request handler(s)/middleware(s)
- **route name**: optional, if omitted, it will be set using the pattern **route path**^PUT
