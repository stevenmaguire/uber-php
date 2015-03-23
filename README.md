# Uber PHP Client

[![Latest Version](https://img.shields.io/github/release/stevenmaguire/uber-php.svg?style=flat-square)](https://github.com/stevenmaguire/uber-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/stevenmaguire/uber-php/master.svg?style=flat-square&1)](https://travis-ci.org/stevenmaguire/uber-php)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/stevenmaguire/uber-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/uber-php/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/stevenmaguire/uber-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/uber-php)
[![Total Downloads](https://img.shields.io/packagist/dt/stevenmaguire/uber-php.svg?style=flat-square)](https://packagist.org/packages/stevenmaguire/uber-php)

A PHP client for authenticating with Uber using OAuth 2 and consuming the API.

## Install

Via Composer

``` bash
$ composer require stevenmaguire/uber-php
```
or update your `composer.json` file to include:

```json
  "require": {
    "stevenmaguire/uber-php": "~1.0"
  }
```
Run `composer update`

## Usage

### Create client

```php
    $client = new Stevenmaguire\Uber\Client(array(
        'access_token' => 'YOUR ACCESS TOKEN',
        'server_token' => 'YOUR SERVER TOKEN',
    ));
```

### Configure defaults


## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/stevenmaguire/uber-php/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
