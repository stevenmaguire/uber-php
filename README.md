# Uber PHP Client

[![Latest Version](https://img.shields.io/github/release/stevenmaguire/uber-php.svg?style=flat-square)](https://github.com/stevenmaguire/uber-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/stevenmaguire/uber-php/master.svg?style=flat-square&1)](https://travis-ci.org/stevenmaguire/uber-php)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/stevenmaguire/uber-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/uber-php/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/stevenmaguire/uber-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stevenmaguire/uber-php)
[![Total Downloads](https://img.shields.io/packagist/dt/stevenmaguire/uber-php.svg?style=flat-square)](https://packagist.org/packages/stevenmaguire/uber-php)

A PHP client for authenticating with Uber using OAuth 2.0 and consuming the API.

*This package is intended to be used for communicating with the Uber API after you've secured an access token from your users. To authenticate users and retrieve access tokens, use [stevenmaguire/oauth2-uber](https://github.com/stevenmaguire/oauth2-uber).*

## Install

Via Composer

``` bash
$ composer require stevenmaguire/uber-php
```

> Note that the required version of PHP is 5.5. If you want use library with PHP 5.4 you should use 1.2.0 version.

## Usage

### Create client

```php
$client = new Stevenmaguire\Uber\Client(array(
    'access_token' => 'YOUR ACCESS TOKEN',
    'server_token' => 'YOUR SERVER TOKEN',
    'use_sandbox'  => true, // optional, default false
    'version'      => 'v1.2', // optional, default 'v1.2'
    'locale'       => 'en_US', // optional, default 'en_US'
));
```
*Please review the [Sandbox](https://developer.uber.com/docs/riders/guides/sandbox) documentation on how to develop and test against these endpoints without making real-world Requests and being charged.*

### Get Products

#### By location:

```php
$products = $client->getProducts(array(
    'latitude' => '41.85582993',
    'longitude' => '-87.62730337'
));
```
[https://developer.uber.com/docs/riders/references/api/v1.2/products-get](https://developer.uber.com/docs/riders/references/api/v1.2/products-get)

#### By Id:

```php
$product = $client->getProduct($productId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/products-product_id-get](https://developer.uber.com/docs/riders/references/api/v1.2/products-product_id-get)

### Get Price Estimates

```php
$estimates = $client->getPriceEstimates(array(
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465'
));
```

[https://developer.uber.com/docs/riders/references/api/v1.2/estimates-price-get](https://developer.uber.com/docs/riders/references/api/v1.2/estimates-price-get)

### Get Time Estimates

```php
$estimates = $client->getTimeEstimates(array(
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337'
));
```

[https://developer.uber.com/docs/riders/references/api/v1.2/estimates-time-get](https://developer.uber.com/docs/riders/references/api/v1.2/estimates-time-get)

### Get Promotions

```php
$promotions = $client->getPromotions(array(
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465'
));
```

[https://developer.uber.com/docs/riders/ride-promotions/introduction](https://developer.uber.com/docs/riders/ride-promotions/introduction)

### Get User Activity

This feature is only available since version `1.1`.

```php
$client->setVersion('v1.2'); // or v1.1
$history = $client->getHistory(array(
    'limit' => 50, // optional
    'offset' => 0 // optional
));
```

[https://developer.uber.com/docs/riders/references/api/v1.2/history-get](https://developer.uber.com/docs/riders/references/api/v1.2/history-get)

### Get User Profile

```php
$profile = $client->getProfile();
```

[https://developer.uber.com/docs/riders/references/api/v1.2/me-get](https://developer.uber.com/docs/riders/references/api/v1.2/me-get)

### Update User Profile

```php
$attributes = array('applied_promotion_codes' => 'PROMO_CODE');
$profileResponse = $client->setProfile($attributes);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/me-patch](https://developer.uber.com/docs/riders/references/api/v1.2/me-patch)

### Get Payment Methods

```php
$paymentMethods = $client->getPaymentMethods();
```

[https://developer.uber.com/docs/riders/references/api/v1.2/payment-methods-get](https://developer.uber.com/docs/riders/references/api/v1.2/payment-methods-get)

### Get Place

```php
$placeId = 'home';
$place = $client->getPlace($placeId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-get](https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-get)

### Update a Place

```php
$placeId = 'home';
$attributes = array('address' => '685 Market St, San Francisco, CA 94103, USA');
$place = $client->setPlace($placeId, $attributes);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-put](https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-put)

### Request A Ride

```php
$request = $client->requestRide(array(
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465',
    'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939', // Optional
    'surge_confirmation_id' => 'e100a670',                  // Optional
    'payment_method_id' => 'a1111c8c-c720-46c3-8534-2fcd'   // Optional
));
```

#### Upfront Fares

Upfront fares means the total fare is known before the ride is taken.

- An end location is required
- There is no surge confirmation flow
- The user should specify a fare_id to confirm consent to the upfront fare
- The user should specify the number of seats that are required for shared products (like UberPOOL)

1. In the products endpoint `GET /products`, products will have the `upfront_fare_enabled` field set to `true`.
2. Use the ride request estimate endpoint `POST /requests/estimate` with the `product_id` to get a `fare_id`. The `fare_id` can be used to lock down an upfront fare and arrival time for a trip. The `fare_id` expires after two minutes. If the `fare_id` is expired or not valid, we return a 422 error.
3. Request the ride using the ride request endpoint `POST /requests` with the `fare_id` returned in the previous step.

[https://developer.uber.com/docs/riders/ride-requests/tutorials/api/best-practices#upfront-fares](https://developer.uber.com/docs/riders/ride-requests/tutorials/api/best-practices#upfront-fares)

#### Surge Confirmation Flow

If the ride request is using a product that has a surge multiplier, the API wrapper will throw an Exception and provide a response body that includes a surge confirmation ID.

```php
try {
    $request = $client->requestRide(array(
        'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
        'start_latitude' => '41.85582993',
        'start_longitude' => '-87.62730337',
        'end_latitude' => '41.87499492',
        'end_longitude' => '-87.67126465'
    ));
} catch (Stevenmaguire\Uber\Exception $e) {
    $body = $e->getBody();
    $surgeConfirmationId = $body['meta']['surge_confirmation']['surge_confirmation_id'];
}
```

[https://developer.uber.com/docs/riders/ride-requests/tutorials/api/best-practices#handling-surge-pricing](https://developer.uber.com/docs/riders/ride-requests/tutorials/api/best-practices#handling-surge-pricing)

### Get Current Ride Details

```php
$request = $client->getCurrentRequest();
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-get](https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-get)

### Get Ride Details

```php
$request = $client->getRequest($requestId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-get](https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-get)

### Update Current Ride Details

```php
$requestDetails = array(
    'end_address' => '685 Market St, San Francisco, CA 94103, USA',
    'end_nickname' => 'da crib',
    'end_place_id' => 'home',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465'
);

$updateRequest = $client->setCurrentRequest($requestDetails);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-patch](https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-patch)

### Update Ride Details

```php
$requestId = '4bfc6c57-98c0-424f-a72e-c1e2a1d49939'
$requestDetails = array(
    'end_address' => '685 Market St, San Francisco, CA 94103, USA',
    'end_nickname' => 'da crib',
    'end_place_id' => 'home',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465'
);

$updateRequest = $client->setRequest($requestId, $requestDetails);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-patch](https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-patch)

### Get Ride Estimate

```php
$requestEstimate = $client->getRequestEstimate(array(
    'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337',
    'end_latitude' => '41.87499492', // optional
    'end_longitude' => '-87.67126465', // optional
));
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-estimate-post](https://developer.uber.com/docs/riders/references/api/v1.2/requests-estimate-post)

### Get Ride Map

```php
$map = $client->getRequestMap($requestId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-map-get](https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-map-get)

### Get Ride Receipt

```php
$receipt = $client->getRequestReceipt($requestId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-receipt-get](https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-receipt-get)

### Cancel Current Ride

```php
$request = $client->cancelCurrentRequest();
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-delete](https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-delete)

### Cancel Ride

```php
$request = $client->cancelRequest($requestId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-delete](https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-delete)

### Create Reminder

```php
$attributes = array(
    'reminder_time' => '1429294463',
    'phone_number' => '555-555-5555',
    'event' => array(
        'time' => '1429294463',
        'name' => 'Frisbee with friends',
        'location' => 'Dolores Park',
        'latitude' => '37.759773',
        'longitude' => '-122.427063',
    ),
    'product_id' => 'a1111c8c-c720-46c3-8534-2fcdd730040d',
    'trip_branding' => array(
        'link_text' => 'View team roster',
        'partner_deeplink' => 'partner://team/9383',
    )
);
$reminder = $client->createReminder($attributes);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/reminders-post](https://developer.uber.com/docs/riders/references/api/v1.2/reminders-post)

### Get Reminder

```php
$reminderId = '4bfc6c57-98c0-424f-a72e-c1e2a1d49939';
$reminder = $client->getReminder($reminderId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-get](https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-get)

### Update Reminder

```php
$reminderId = '4bfc6c57-98c0-424f-a72e-c1e2a1d49939';
$attributes = array(
    'reminder_time' => '1429294463',
    'phone_number' => '555-555-5555',
    'event' => array(
        'time' => '1429294463',
        'name' => 'Frisbee with friends',
        'location' => 'Dolores Park',
        'latitude' => '37.759773',
        'longitude' => '-122.427063',
    ),
    'product_id' => 'a1111c8c-c720-46c3-8534-2fcdd730040d',
    'trip_branding' => array(
        'link_text' => 'View team roster',
        'partner_deeplink' => 'partner://team/9383',
    )
);
$reminder = $client->setReminder($reminderId, $attributes);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-patch](https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-patch)

### Cancel Reminder

```php
$reminderId = '4bfc6c57-98c0-424f-a72e-c1e2a1d49939';
$reminder = $client->cancelReminder($reminderId);
```

[https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-delete](https://developer.uber.com/docs/riders/references/api/v1.2/reminders-reminder_id-delete)

### Get Driver Profile

```php
$profile = $client->getDriverProfile();
```

[https://developer.uber.com/docs/drivers/references/api/v1/partners-me-get](https://developer.uber.com/docs/drivers/references/api/v1/partners-me-get)

### Get Driver Payments

```php
$profile = $client->getDriverPayments(array(
    'limit' => 50, // optional
    'offset' => 0 // optional
));
```

[https://developer.uber.com/docs/drivers/references/api/v1/partners-payments-get](https://developer.uber.com/docs/drivers/references/api/v1/partners-payments-get)

### Get Driver Trips

```php
$profile = $client->getDriverTrips(array(
    'limit' => 50, // optional
    'offset' => 0 // optional
));
```

[https://developer.uber.com/docs/drivers/references/api/v1/partners-trips-get](https://developer.uber.com/docs/drivers/references/api/v1/partners-trips-get)

### Rate Limiting

> This feature is only supported for `v1` version of the API.

Rate limiting is implemented on the basis of a specific client's secret token. By default, 1,000 requests per hour can be made per secret token.

When consuming the service with this package, your rate limit status will be made available within the client.

```php
$product = $client->getProduct($productId);

$rateLimit = $client->getRateLimit();

$rateLimit->getLimit();        // Rate limit capacity per period
$rateLimit->getRemaining();    // Requests remaining in current period
$rateLimit->getReset();        // Timestamp in UTC time when the next period will begin
```
These values will update after each request. `getRateLimit` will return null after the client is created and before the first successful request.

[https://developer.uber.com/v1/api-reference/#rate-limiting](https://developer.uber.com/v1/api-reference/#rate-limiting)

### Using the Sandbox

Modify the status of an ongoing sandbox Request.

> These methods will throw `Stevenmaguire\Uber\Exception` when invoked while the client is not in sandbox mode. The underlying API endpoints have no effect unless you are using the sandbox environment.

```php
$request = $client->requestRide(array(
    'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
    'start_latitude' => '41.85582993',
    'start_longitude' => '-87.62730337',
    'end_latitude' => '41.87499492',
    'end_longitude' => '-87.67126465'
));

$updateRequest = $client->setSandboxRequest($request->request_id, array('status' => 'accepted'));
```
[https://developer.uber.com/v1/sandbox/#request](https://developer.uber.com/v1/sandbox/#request)

Simulate the possible responses the Request endpoint will return when requesting a particular product, such as surge pricing, against the Sandbox.

```php
$product = $client->getProduct($productId);

$updateProduct = $client->setSandboxProduct($productId, array('surge_multiplier' => 2.2, 'drivers_available' => false));
```

[https://developer.uber.com/v1/sandbox/#product-types](https://developer.uber.com/v1/sandbox/#product-types)

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/stevenmaguire/uber-php/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
