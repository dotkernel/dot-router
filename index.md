# dot-router

Dotkernel component to build complex routes, based on [mezzio/mezzio-fastroute](https://github.com/mezzio/mezzio-fastroute).

> dot-router is a wrapper on top of [mezzio/mezzio-fastroute](https://github.com/mezzio/mezzio-fastroute)

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-router/.

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-router)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-router/2.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-router)](https://github.com/dotkernel/dot-router/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-router)](https://github.com/dotkernel/dot-router/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-router)](https://github.com/dotkernel/dot-router/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-router)](https://github.com/dotkernel/dot-router/blob/2.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-router/actions/workflows/continuous-integration.yml/badge.svg?branch=2.0)](https://github.com/dotkernel/dot-router/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-router/graph/badge.svg?token=ubNnFctuDR)](https://codecov.io/gh/dotkernel/dot-router)
[![PHPStan](https://github.com/dotkernel/dot-router/actions/workflows/static-analysis.yml/badge.svg?branch=2.0)](https://github.com/dotkernel/dot-router/actions/workflows/static-analysis.yml)

## Versions

| Version | Supported PHP versions | Documentation |
| --- | --- | --- |
| 2.x | 8.3, 8.4, 8.5, 8.6 | https://docs.dotkernel.org/dot-router/v2/overview/ |
| 1.x | 8.2, 8.3, 8.4, 8.5 | https://docs.dotkernel.org/dot-router/v1/overview/ |

Upgrading from 1.x? Read the [upgrade guide](https://docs.dotkernel.org/dot-router/v2/upgrading/).

## Requirements

- **PHP**: 8.3, 8.4, 8.5 or 8.6
- **laminas/laminas-stratigility**: ^3.0 || ^4.0
- **mezzio/mezzio-fastroute**: ^3.15

## Setup

Run the following command in your application's root directory:

```shell
composer require dotkernel/dot-router
```

Open your application's `config/config.php` and add the following line:

```php
Dot\Router\ConfigProvider::class,
```
