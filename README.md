# Yelp PHP Client

[![Latest Version](https://img.shields.io/github/release/stevenmaguire/yelp-php.svg?style=flat-square)](https://github.com/stevenmaguire/yelp-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/stevenmaguire/yelp-php/master.svg?style=flat-square&1)](https://travis-ci.org/stevenmaguire/yelp-php)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/stevenmaguire/yelp-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/yelp-php/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/stevenmaguire/yelp-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/yelp-php)
[![Total Downloads](https://img.shields.io/packagist/dt/stevenmaguire/yelp-php.svg?style=flat-square)](https://packagist.org/packages/stevenmaguire/yelp-php)

A PHP client for authenticating with Yelp using OAuth 1 and consuming the search API.

## Install

Via Composer

``` bash
$ composer require stevenmaguire/yelp-php
```
or update your `composer.json` file to include:

```json
  "require": {
    "stevenmaguire/yelp-php": ">=1.0.0"
  }
```
Run `composer update`

## Usage

### Create client

```php
    $client = new Stevenmaguire\Yelp\Client(array(
        'consumer_key' => 'YOUR COSUMER KEY',
        'consumer_secret' => 'YOUR CONSUMER SECRET',
        'token' => 'YOUR TOKEN',
        'token_secret' => 'YOUR TOKEN SECRET',
        'api_host' => 'api.yelp.com'
    ));
```

### Search by keyword and location

```php
$results = $client->search(array('term' => 'Sushi', 'location' => 'Chicago, IL'));
```

### Locate details for a specific business by Yelp business id

```php
$results = $client->getBusiness('union-chicago-3');
```

### Configure defaults

```php
$client->setDefaultLocation('Chicago, IL')  // default location for all searches if location not provided
    ->setDefaultTerm('Sushi')               // default keyword for all searches if term not provided
    ->setSearchLimit(20);                   // number of records to return
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/stevenmaguire/trello-php/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
