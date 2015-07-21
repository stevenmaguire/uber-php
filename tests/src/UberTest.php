<?php namespace Stevenmaguire\Uber\Test;

use Stevenmaguire\Uber\Client as Uber;
use Mockery as m;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException as HttpClientException;

class UberTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Uber([
            'access_token'  =>  getenv('UBER_ACCESS_TOKEN'),
            'server_token'  =>  getenv('UBER_SERVER_TOKEN'),
            'use_sandbox'   =>  getenv('UBER_USE_SANDBOX'),
            'version'       =>  getenv('UBER_VERSION'),
            'locale'        =>  getenv('UBER_LOCALE'),
        ]);
    }

    public function test_Configuration()
    {
        $client = new Uber([
            'access_token'  =>  getenv('UBER_ACCESS_TOKEN'),
            'server_token'  =>  getenv('UBER_SERVER_TOKEN'),
            'use_sandbox'   =>  getenv('UBER_USE_SANDBOX'),
            'version'       =>  getenv('UBER_VERSION'),
            'locale'        =>  getenv('UBER_LOCALE'),
        ]);

        $this->assertEquals($client->getAccessToken(), getenv('UBER_ACCESS_TOKEN'));
        $this->assertEquals($client->getServerToken(), getenv('UBER_SERVER_TOKEN'));
        $this->assertEquals($client->getUseSandbox(), getenv('UBER_USE_SANDBOX'));
        $this->assertEquals($client->getVersion(), getenv('UBER_VERSION'));
        $this->assertEquals($client->getLocale(), getenv('UBER_LOCALE'));
    }

    /**
     * @expectedException Stevenmaguire\Uber\Exception
     */
    public function test_Configuration_Will_Not_Accept_Non_Property_Config()
    {
        $client = new Uber([
            'non_existent_property'  =>  'test',
        ]);

        $client->getNonExistentProperty();
    }

    public function test_Url_Includes_Version()
    {
        $version = uniqid();
        $this->client->setVersion($version);

        $url = $this->client->getUrlFromPath('/');

        $this->assertContains($version, $url);
    }

    public function test_Url_Omits_Version_When_Not_Provided()
    {
        $this->client->setVersion(null);

        $url = $this->client->getUrlFromPath('/');

        $this->assertStringEndsNotWith('//', $url);
    }

    public function test_Headers_Include_Bearer_When_Access_Token_Provided()
    {
        $access_token = uniqid();
        $this->client->setAccessToken($access_token);

        $headers = $this->client->getHeaders();

        $this->assertTrue(in_array('Authorization', array_keys($headers)));
        $this->assertEquals('Bearer '.$access_token, $headers['Authorization']);
    }

    public function test_Headers_Include_Token_When_Access_Token_Not_Provided()
    {
        $server_token = uniqid();
        $this->client->setServerToken($server_token)->setAccessToken(null);

        $headers = $this->client->getHeaders();

        $this->assertTrue(in_array('Authorization', array_keys($headers)));
        $this->assertEquals('Token '.$server_token, $headers['Authorization']);
    }

    public function test_Headers_Include_Empty_Token_When_Access_And_Server_Token_Not_Provided()
    {
        $this->client->setServerToken(null)->setAccessToken(null);

        $headers = $this->client->getHeaders();

        $this->assertTrue(in_array('Authorization', array_keys($headers)));
        $this->assertEquals('Token', $headers['Authorization']);
    }

    public function test_Headers_Include_AcceptLanguage_When_Locale_Provided()
    {
        $locale = $this->client->getLocale();

        $headers = $this->client->getHeaders();

        $this->assertTrue(in_array('Accept-Language', array_keys($headers)));
        $this->assertEquals($locale, $headers['Accept-Language']);
    }

    public function test_Headers_Include_Empty_AcceptLanguage_When_Locale_Not_Provided()
    {
        $headers = $this->client->setLocale(null)->getHeaders();

        $this->assertTrue(in_array('Accept-Language', array_keys($headers)));
        $this->assertEmpty($headers['Accept-Language']);
    }

    public function test_Get_Products()
    {
        $params = [
            'latitude' => '41.85582993',
            'longitude' => '-87.62730337'
        ];

        $this->client->setAccessToken(null);

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"products": [{"product_id": "327f7914-cd12-4f77-9e0c-b27bac580d03","description": "The original Uber","display_name": "UberBLACK","capacity": 4,"image": "http://..."},{"product_id": "955b92da-2b90-4f32-9586-f766cee43b99","description": "Room for everyone","display_name": "UberSUV","capacity": 6,"image": "http://..."},{"product_id": "622237e-c1e4-4523-b6e7-e1ac53f625ed","description": "Taxi without the hassle","display_name": "uberTAXI","capacity": 4,"image": "http://..."},{"product_id": "b5e74e96-5d27-4caf-83e9-54c030cd6ac5","description": "The low-cost Uber","display_name": "uberX","capacity": 4,"image": "http://..."}]}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/products'), ['headers' => $this->client->getHeaders(), 'query' => $params])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $products = $this->client->getProducts($params);
        $this->assertNull($this->client->getAccessToken());
    }

    public function test_Get_Product()
    {
        $product_id = 'mock_product_id';

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"product_id": "'.$product_id.'","description": "The original Uber","display_name": "UberBLACK","capacity": 4,"image": "http://..."}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/products/'.$product_id), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $product = $this->client->getProduct($product_id);
    }

    public function test_Get_Price_Estimates()
    {
        $params = [
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ];

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"prices": [{"product_id": "08f17084-23fd-4103-aa3e-9b660223934b","currency_code": "USD","display_name": "UberBLACK","estimate": "$23-29","low_estimate": 23,"high_estimate": 29,"surge_multiplier": 1,"duration": 640,"distance": 5.34},{"product_id": "9af0174c-8939-4ef6-8e91-1a43a0e7c6f6","currency_code": "USD","display_name": "UberSUV","estimate": "$36-44","low_estimate": 36,"high_estimate": 44,"surge_multiplier": 1.25,"duration": 640,"distance": 5.34},{"product_id": "aca52cea-9701-4903-9f34-9a2395253acb","currency_code": null,"display_name": "uberTAXI","estimate": "Metered","low_estimate": null,"high_estimate": null,"surge_multiplier": 1,"duration": 640,"distance": 5.34},{"product_id": "a27a867a-35f4-4253-8d04-61ae80a40df5","currency_code": "USD","display_name": "uberX","estimate": "$15","low_estimate": 15,"high_estimate": 15,"surge_multiplier": 1,"duration": 640,"distance": 5.34}]}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/estimates/price'), ['headers' => $this->client->getHeaders(), 'query' => $params])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $estimates = $this->client->getPriceEstimates($params);
    }

    public function test_Get_Time_Estimates()
    {
        $params = [
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
        ];

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"times": [{"product_id": "5f41547d-805d-4207-a297-51c571cf2a8c","display_name": "UberBLACK","estimate": 410},{"product_id": "694558c9-b34b-4836-855d-821d68a4b944","display_name": "UberSUV","estimate": 535},{"product_id": "65af3521-a04f-4f80-8ce2-6d88fb6648bc","display_name": "uberTAXI","estimate": 294},{"product_id": "17b011d3-65be-421d-adf6-a5480a366453","display_name": "uberX","estimate": 288}]}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/estimates/time'), ['headers' => $this->client->getHeaders(), 'query' => $params])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $estimates = $this->client->getTimeEstimates($params);
    }

    public function test_Get_Promotions()
    {
        $params = [
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ];

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"display_text": "Free ride up to $30","localized_value": "$30","type": "trip_credit"}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/promotions'), ['headers' => $this->client->getHeaders(), 'query' => $params])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $promotions = $this->client->getPromotions($params);
    }

    public function test_Get_History()
    {
        $params = [
            'limit' => 1,
            'offset' => 1
        ];

        $this->client->setVersion('v1.1');

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"offset": 0,"limit": 1,"count": 5,"history": [{"uuid": "7354db54-cc9b-4961-81f2-0094b8e2d215","request_time": 1401884467,"product_id": "edf5e5eb-6ae6-44af-bec6-5bdcf1e3ed2c","status": "completed","distance": 0.0279562,"start_time": 1401884646,"end_time": 1401884732}]}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/history'), ['headers' => $this->client->getHeaders(), 'query' => $params])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $history = $this->client->getHistory($params);
    }

    public function test_Get_Profile()
    {
        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"first_name": "Uber","last_name": "Developer","email": "developer@uber.com","picture": "https://...","promo_code": "teypo","uuid": "91d81273-45c2-4b57-8124-d0165f8240c0"}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/me'), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $profile = $this->client->getProfile();
    }

    public function test_Get_Request_Estimate()
    {
        $params = [
            'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ];

        $postResponse = m::mock('GuzzleHttp\Psr7\Response');
        $postResponse->shouldReceive('getBody')->times(1)->andReturn('{"price":{"surge_multiplier]": 1,"minimum": 15,"surge_confirmation_href": "","currency_code": "USD","surge_confirmation_id]": ""},"trip": {"pickup_estimate": 10}}');
        $postResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('post')
            ->with($this->client->getUrlFromPath('/requests/estimate'), ['headers' => $this->client->getHeaders(), 'json' => $params])
            ->times(1)->andReturn($postResponse);

        $this->client->setHttpClient($http_client);

        $requestEstimate = $this->client->getRequestEstimate($params);
    }

    public function test_Request_Ride()
    {
        $params = [
            'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ];

        $this->client->setUseSandbox(true);

        $postResponse = m::mock('GuzzleHttp\Psr7\Response');
        $postResponse->shouldReceive('getBody')->times(1)->andReturn('{"request_id": "852b8fdd-4369-4659-9628-e122662ad257","status": "processing","vehicle": null,"driver": null,"location": null,"eta": 5,"surge_multiplier": null}');
        $postResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('post')
            ->with($this->client->getUrlFromPath('/requests'), ['headers' => $this->client->getHeaders(), 'json' => $params])
            ->times(1)->andReturn($postResponse);

        $this->client->setHttpClient($http_client);

        $request = $this->client->requestRide($params);
    }

    public function test_Get_Request()
    {
        $request_id = 'mock_request_id';

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"request_id": "'.$request_id.'","status": "processing","vehicle": null,"driver": null,"location": null,"eta": 5,"surge_multiplier": null}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/requests/'.$request_id), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $request = $this->client->getRequest($request_id);
    }

    public function test_Get_Request_Receipt()
    {
        $request_id = 'mock_request_id';

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"request_id": "b5512127-a134-4bf4-b1ba-fe9f48f56d9d", "charges": [{"name": "Base Fare", "amount": "2.20", "type": "base_fare" }, {"name": "Distance", "amount": "2.75", "type": "distance", }, {"name": "Time", "amount": "3.57", "type": "time"} ], "surge_charge" : {"name": "Surge x1.5", "amount": "4.26", "type": "surge"}, "charge_adjustments" : [{"name": "Promotion", "amount": "-2.43", "type": "promotion", }, { "name": "Safe Rides Fee", "amount": "1.00", "type": "safe_ride_fee"}, { "name": "Rounding Down", "amount": "0.78", "type": "rounding_down"}], "normal_fare": "$8.52", "subtotal": "$12.78", "total_charged": "$5.92", "total_owed": null, "currency_code": "USD", "duration": "00:11:35", "distance": "1.49", "distance_label": "miles"}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/requests/'.$request_id.'/receipt'), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $receipt = $this->client->getRequestReceipt($request_id);
    }

    public function test_Get_Request_Map()
    {
        $request_id = 'mock_request_id';

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"request_id":"'.$request_id.'","href":"https://trip.uber.com/abc123"}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')
            ->with($this->client->getUrlFromPath('/requests/'.$request_id.'/map'), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $map = $this->client->getRequestMap($request_id);
    }

    public function test_Cancel_Request()
    {
        $request_id = 'mock_request_id';

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn(null);
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('delete')
            ->with($this->client->getUrlFromPath('/requests/'.$request_id), ['headers' => $this->client->getHeaders()])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $cancel_request = $this->client->cancelRequest($request_id);
    }

    public function test_Get_Existing_Properties()
    {
        $locale = $this->client->getLocale();

        $this->assertEquals($locale, getenv('UBER_LOCALE'));
    }

    public function test_Set_Request()
    {
        $request_id = 'mock_request_id';
        $request_body = ['status' => uniqid()];

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn(null);
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('put')
            ->with($this->client->getUrlFromPath('/sandbox/requests/'.$request_id), ['headers' => $this->client->getHeaders(), 'json' => $request_body])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $request = $this->client->setRequest($request_id, $request_body);
    }

    public function test_Set_Product()
    {
        $product_id = 'mock_request_id';
        $request_body = ['surge_multiplier' => uniqid()];

        $getResponse = m::mock('GuzzleHttp\Psr7\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn(null);
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000, 955, strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('put')
            ->with($this->client->getUrlFromPath('/sandbox/products/'.$product_id), ['headers' => $this->client->getHeaders(), 'json' => $request_body])
            ->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $request = $this->client->setProduct($product_id, $request_body);
    }

    /**
     * @expectedException Stevenmaguire\Uber\Exception
     */
    public function test_Get_Non_Existing_Properties()
    {
        $result = $this->client->{'get'.rand(1111,9999)}();
    }

    public function test_Set_Existing_Properties()
    {
        $var = uniqid();
        $locale = $this->client->setLocale($var)->getLocale();

        $this->assertEquals($locale, $var);
    }

    /**
     * @expectedException Stevenmaguire\Uber\Exception
     */
    public function test_Set_Non_Existing_Properties()
    {
        $result = $this->client->{'set'.rand(1111,9999)}();
    }

    /**
     * @expectedException Stevenmaguire\Uber\Exception
     */
    public function test_Throws_Exception_On_Http_Errors()
    {
        $params = [];
        $responseCode = 429;
        $responseHeaders = ['Content-Length' => 0];
        $mock = new MockHandler([
            new Response($responseCode, $responseHeaders)
        ]);
        $handler = HandlerStack::create($mock);

        $http_client = new HttpClient(['handler' => $handler]);

        $this->client->setHttpClient($http_client);

        $this->client->getProducts($params);
    }

    public function test_Http_Exceptions_Include_Meta_From_Uber()
    {
        $params = [];
        $responseCode = 409;
        $responseReason = "Conflict";
        $responsePayload = '{"meta":{"surge_confirmation":{"href":"https:\/\/api.uber.com\/v1\/surge-confirmations\/e100a670","surge_confirmation_id":"e100a670"}},"errors":[{"status":'.$responseCode.',"code":"surge","title":"Surge pricing is currently in effect for this product."}]}';
        $responseHeaders = [
            "Content-Type" => "application/json; charset=UTF-8",
            "Content-Length" => strlen($responsePayload),
            "Accept" => "application/json"
        ];

        $mock = new MockHandler([
            new Response($responseCode, $responseHeaders, $responsePayload)
        ]);
        $handler = HandlerStack::create($mock);

        $http_client = new HttpClient(['handler' => $handler]);

        $this->client->setHttpClient($http_client);

        try {
            $this->client->getProducts($params);
        } catch (\Stevenmaguire\Uber\Exception $e) {
            $this->assertContains($responseReason, $e->getMessage());
            $this->assertEquals($responseCode, $e->getCode());
            $this->assertEquals($responsePayload, json_encode($e->getBody()));
        }
    }

    public function test_Client_Exceptions_Throw_Uber_Exception()
    {
        $params = [];
        $exception = new HttpClientException(
            uniqid(),
            m::mock('Psr\Http\Message\RequestInterface')
        );
        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get')->times(1)->andThrow($exception);

        $this->client->setHttpClient($http_client);

        try {
            $products = $this->client->getProducts($params);
        } catch (\Stevenmaguire\Uber\Exception $e) {
            $this->assertEquals(500, $e->getCode());
        }
    }
}
