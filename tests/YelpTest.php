<?php

use Stevenmaguire\Yelp\Client as Yelp;

class YelpTest extends TestCase
{
    public function setUp()
    {
        $this->client = new Yelp([
            'consumer_key' =>       '9pVHHgi4i9-rxR68CGyxOw',
            'consumer_secret' =>    '5CT8iAq2gx_ESSZ9-qhigTYKuc8',
            'token' =>              'wMzHE9j1ek2iJL-bcf2SnxC6SPLa1Mza',
            'token_secret' =>       'TkjSQGeULwu1EXO7Opouw3ahLqA',
            'api_host' =>           'api.yelp.com'
        ]);
    }

    public function test_It_Can_Find_Business_By_Id()
    {
        $business_id = 'the-motel-bar-chicago';

        $business = $this->client->getBusiness($business_id);

        $this->assertInstanceOf('stdClass', $business);
        $this->assertEquals($business_id, $business->id);
    }

    public function test_It_Can_Search_Bars_In_Chicago()
    {
        $term = 'bars';
        $location = 'Chicago, IL';
        $attributes = ['term' => $term, 'location' => $location];

        $results = $this->client->search($attributes);

        $this->assertInstanceOf('stdClass', $results);
        $this->assertNotEmpty($results->businesses);
        $this->assertEquals(3, count($results->businesses));
    }

    public function test_It_Can_Set_Defaults()
    {
        $default_term = 'stores';
        $default_location = 'Chicago, IL';
        $default_limit = 10;

        $results = $this->client->setDefaultLocation($default_location)
            ->setDefaultTerm($default_term)
            ->setSearchLimit($default_limit)
            ->search();

        $this->assertInstanceOf('stdClass', $results);
        $this->assertNotEmpty($results->businesses);
        $this->assertEquals($default_limit, count($results->businesses));
    }
}
