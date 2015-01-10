# Yelp PHP Client

[![Latest Version](https://img.shields.io/github/release/stevenmaguire/trello-oauth1-server.svg?style=flat-square)](https://github.com/stevenmaguire/trello-oauth1-server/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/stevenmaguire/trello-oauth1-server/master.svg?style=flat-square&1)](https://travis-ci.org/stevenmaguire/trello-oauth1-server)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/stevenmaguire/trello-oauth1-server.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/trello-oauth1-server/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/stevenmaguire/trello-oauth1-server.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/trello-oauth1-server)
[![Total Downloads](https://img.shields.io/packagist/dt/stevenmaguire/trello-oauth1-server.svg?style=flat-square)](https://packagist.org/packages/stevenmaguire/trello-oauth1-server)

A PHP client for authenticating with Yelp using OAuth 1 and consuming the search API.

## Install

Via Composer

``` bash
$ composer require stevenmaguire/yelp-php
```
or update your `composer.json` file to include:

```json
  "require": {
    "stevenmaguire/yelp-php": ">=0.0.1"
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
```

### Search by keyword and location

```php
$results = $client->search('Sushi', 'Chicago, IL');
```

### Locate details for a specific business by Yelp business id

```php
$results = $client->get_business('union-chicago-3');
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
