<?php namespace Stevenmaguire\Yelp;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\ClientException;

class Client
{
    /**
     * API host url
     *
     * @var string
     */
    private $api_host;

    /**
     * Consumer key
     *
     * @var string
     */
    private $consumer_key;

    /**
     * Consumer secret
     *
     * @var string
     */
    private $consumer_secret;

    /**
     * Access token
     *
     * @var string
     */
    private $token;

    /**
     * Access token secret
     *
     * @var string
     */
    private $token_secret;

    /**
     * Default search term
     *
     * @var string
     */
    private $default_term = 'bar';

    /**
     * Default location
     *
     * @var string
     */
    private $default_location = 'Chicago, IL';

    /**
     * Default search limit
     *
     * @var integer
     */
    private $search_limit = 3;

    /**
     * Search path
     *
     * @var string
     */
    private $search_path = '/v2/search/';

    /**
     * Business path
     *
     * @var string
     */
    private $business_path = '/v2/business/';

    /**
     * Create new client
     *
     * @param array $configuration
     */
    public function __construct($configuration = [])
    {
        $this->parseConfiguration($configuration);

        $this->consumer_key = $configuration['consumer_key'];
        $this->consumer_secret = $configuration['consumer_secret'];
        $this->token = $configuration['token'];
        $this->token_secret = $configuration['token_secret'];
        $this->api_host = $configuration['api_host'];
    }

    /**
     * Set default location
     *
     * @param string $location
     *
     * @return Client
     */
    public function setDefaultLocation($location)
    {
        $this->default_location = $location;
        return $this;
    }

    /**
     * Set default term
     *
     * @param string $term
     *
     * @return Client
     */
    public function setDefaultTerm($term)
    {
        $this->default_term = $term;
        return $this;
    }

    /**
     * Set search limit
     *
     * @param integer $limit
     *
     * @return Client
     */
    public function setSearchLimit($limit)
    {
        if (is_int($limit)) {
            $this->search_limit = $limit;
        }
        return $this;
    }

    /**
     * Query the Search API by a search term and location
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function search($attributes = [])
    {
        $query_string = $this->buildQueryParams($attributes);
        $search_path = $this->search_path . "?" . $query_string;

        return $this->request($search_path);
    }

    /**
     * Query the Business API by business_id
     *
     * @param    string   $business_id      The ID of the business to query
     *
     * @return   stdClass                   The JSON response from the request
     */
    public function getBusiness($business_id)
    {
        $business_path = $this->business_path . $business_id;

        return $this->request($business_path);
    }

    /**
     * Parse configuration using defaults
     *
     * @param  array $configuration
     *
     * @return array $configuration
     */
    private function parseConfiguration(&$configuration = [])
    {
        $defaults = array(
            'consumer_key' => null,
            'consumer_secret' => null,
            'token' => null,
            'token_secret' => null,
            'api_host' => 'api.yelp.com'
        );

        $configuration = array_merge($defaults, $configuration);
    }

    /**
     * Build query string params using defaults
     *
     * @param  array $attributes
     *
     * @return string
     */
    private function buildQueryParams($attributes = [])
    {
        $defaults = array(
            'term' => $this->default_term,
            'location' => $this->default_location,
            'limit' => $this->search_limit
        );
        $attributes = array_merge($defaults, $attributes);

        return http_build_query($attributes);
    }

    /**
     * Makes a request to the Yelp API and returns the response
     *
     * @param    string $path    The path of the APi after the domain
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    private function request($path)
    {
        $client = new HttpClient;
        $oauth = new Oauth1([
            'consumer_key'    => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
            'token'           => $this->token,
            'token_secret'    => $this->token_secret
        ]);

        $client->getEmitter()->attach($oauth);
        $url = $this->buildUnsignedUrl($this->api_host, $path);

        try {
            $response = $client->get($url, ['auth' => 'oauth']);
        } catch (ClientException $e) {
            throw new Exception($e->getMessage());
        }

        return json_decode($response->getBody());
    }

    /**
     * Build unsigned url
     *
     * @param  string   $host
     * @param  string   $path
     *
     * @return string   Unsigned url
     */
    private function buildUnsignedUrl($host, $path)
    {
        return "http://" . $host . $path;
    }
}
