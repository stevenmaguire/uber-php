<?php namespace Stevenmaguire\Uber;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\ClientException;

class Client
{
    /**
     * Access token
     *
     * @var string
     */
    private $access_token;

    /**
     * Server token
     *
     * @var string
     */
    private $server_token;

    /**
     * Use sandbox API
     *
     * @var bool
     */
    private $use_sandbox;

    /**
     * Version
     *
     * @var string
     */
    private $version;

    /**
     * Create new client
     *
     * @param array $configuration
     */
    public function __construct($configuration = [])
    {
        $configuration = $this->parseConfiguration($configuration);

        $this->access_token = $configuration['access_token'];
        $this->server_token = $configuration['server_token'];
        $this->use_sandbox = $configuration['use_sandbox'];
        $this->version = $configuration['version'];
    }

    /**
     * The Products endpoint returns information about the Uber products
     * offered at a given location. The response includes the display name and
     * other details about each product, and lists the products in the proper
     * display order.
     *
     * Some Products, such as experiments or promotions such as UberPOOL and
     * UberFRESH, will not be returned by this endpoint.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getProducts($attributes = [])
    {
        return $this->request('get', 'products', $attributes);
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
    private function parseConfiguration($configuration = [])
    {
        $defaults = array(
            'access_token'  =>  null,
            'server_token'  =>  null,
            'use_sandbox'   =>  false,
            'version'   =>  'v1',
        );

        return array_merge($defaults, $configuration);
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
     * Makes a request to the Uber API and returns the response
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
