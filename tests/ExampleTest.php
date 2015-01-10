<?php

use Stevenmaguire\Yelp\Client as Yelp;

class ExampleTest extends TestCase
{
    public function test_it_works()
    {
        $client = new Yelp([
            'consumer_key' =>       '9pVHHgi4i9-rxR68CGyxOw',
            'consumer_secret' =>    '5CT8iAq2gx_ESSZ9-qhigTYKuc8',
            'token' =>              'wMzHE9j1ek2iJL-bcf2SnxC6SPLa1Mza',
            'token_secret' =>       'TkjSQGeULwu1EXO7Opouw3ahLqA',
            'api_host' =>           'api.yelp.com'
        ]);

        //print_r($client);

        //print_r($client->get_business('the-motel-bar-chicago'));
    }
}
