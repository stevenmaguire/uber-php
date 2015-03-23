<?php namespace Stevenmaguire\Uber\Test;

use Stevenmaguire\Uber\Client as Uber;

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
        $products = $this->client->getProducts([
            'latitude' => '41.85582993',
            'longitude' => '-87.62730337'
        ]);

        print_r($products);
    }
}
