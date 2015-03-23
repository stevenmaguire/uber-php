<?php namespace Stevenmaguire\Uber\Test;

use Stevenmaguire\Uber\Client as Uber;
use Mockery as m;

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

    public function test_Get_Products()
    {
        $getResponse = m::mock('GuzzleHttp\Message\Response');
        $getResponse->shouldReceive('getBody')->times(1)->andReturn('{"products": [{"product_id": "327f7914-cd12-4f77-9e0c-b27bac580d03","description": "The original Uber","display_name": "UberBLACK","capacity": 4,"image": "http://..."},{"product_id": "955b92da-2b90-4f32-9586-f766cee43b99","description": "Room for everyone","display_name": "UberSUV","capacity": 6,"image": "http://..."},{"product_id": "622237e-c1e4-4523-b6e7-e1ac53f625ed","description": "Taxi without the hassle","display_name": "uberTAXI","capacity": 4,"image": "http://..."},{"product_id": "b5e74e96-5d27-4caf-83e9-54c030cd6ac5","description": "The low-cost Uber","display_name": "uberX","capacity": 4,"image": "http://..."}]}');
        $getResponse->shouldReceive('getHeader')->times(3)->andReturnValues([1000,955,strtotime("+1 day")]);

        $http_client = m::mock('GuzzleHttp\Client');
        $http_client->shouldReceive('get','send')->times(1)->andReturn($getResponse);

        $this->client->setHttpClient($http_client);

        $products = $this->client->getProducts([
            'latitude' => '41.85582993',
            'longitude' => '-87.62730337'
        ]);
    }

    public function test_Get_Estimates()
    {
        $estimates = $this->client->getPriceEstimates([
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ]);

        $estimates = $this->client->getTimeEstimates([
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
        ]);
    }

    public function test_Get_Promotions()
    {
        $promotions = $this->client->getPromotions([
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ]);
    }

    public function test_Get_History()
    {
        $this->client->setVersion('v1.1');
        $history = $this->client->getHistory([
            'limit' => 1,
            'offset' => 1
        ]);
    }

    public function test_Get_Profile()
    {
        $profile = $this->client->getProfile();
    }

    public function test_Requests()
    {
        $this->client->setUseSandbox(true);
        $request = $this->client->requestRide([
            'product_id' => '4bfc6c57-98c0-424f-a72e-c1e2a1d49939',
            'start_latitude' => '41.85582993',
            'start_longitude' => '-87.62730337',
            'end_latitude' => '41.87499492',
            'end_longitude' => '-87.67126465',
        ]);

        $request_id = $request->request_id;

        $request_details = $this->client->getRequest($request_id);

        $request_map = $this->client->getRequestMap($request_id);

        $cancel_request = $this->client->cancelRequest($request_id);
    }
}
