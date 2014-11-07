Yelp PHP Client
========
A php client for consuming Yelp API

Installation
------------

Update your `composer.json` file to include:

```php 
  "require": {
    ...
    "stevenmaguire/yelp-php": ">=0.0.1"
    ...
  }
```
  
Run `composer update`

Usage
-----

Create client

```php
    $client = new Stevenmaguire\Yelp\Client(array(
        'consumer_key' => 'YOUR COSUMER KEY',
        'consumer_secret' => 'YOUR CONSUMER SECRET',
        'token' => 'YOUR TOKEN',
        'token_secret' => 'YOUR TOKEN SECRET',
        'api_host' => 'api.yelp.com'
    ));
```

Search by keyword and location

```php
$results = $client->search('Sushi', 'Chicago, IL');
```

Locate details for a specific business by Yelp business id

```php
$results = $client->get_business('union-chicago-3');
```

Configure defaults
```php
$client->setDefaultLocation('Chicago, IL')  // default location for all searches if location not provided
    ->setDefaultTerm('Sushi')               // default keyword for all searches if term not provided
    ->setSearchLimit(20);                   // number of records to return
```
