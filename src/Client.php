<?php namespace Stevenmaguire\Uber;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;

class Client
{
    use GetSetTrait;

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
     * Locale
     *
     * @var string
     */
    private $locale;

    /**
     * Rate limit
     *
     * @var RateLimit
     */
    private $rate_limit = null;

    /**
     * Http client
     *
     * @var HttpClient
     */
    private $http_client;

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
        $this->locale = $configuration['locale'];

        $this->http_client = new HttpClient;
    }

    /**
     * Cancel a single request
     *
     * @param    string   $request_id    Request id
     *
     * @return   stdClass               The JSON response from the request
     */
    public function cancelRequest($request_id)
    {
        return $this->request('delete', 'requests/'.$request_id);
    }

    /**
     * The User Activity endpoint returns a limited amount of data about a
     * user's lifetime activity with Uber. The response will include pickup and
     * dropoff times, the distance of past requests, and information about
     * which products were requested.
     *
     * The history array in the response will have a maximum length based on
     * the limit parameter. The response value count may exceed limit,
     * therefore subsequent API requests may be necessary.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getHistory($attributes = [])
    {
        return $this->request('get', 'history', $attributes);
    }

    /**
     * The Price Estimates endpoint returns an estimated price range for each
     * product offered at a given location. The price estimate is provided as
     * a formatted string with the full price range and the localized currency
     * symbol.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getPriceEstimates($attributes = [])
    {
        return $this->request('get', 'estimates/price', $attributes);
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
     * Get a single product
     *
     * @param    string   $product_id    Product id
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getProduct($product_id)
    {
        return $this->request('get', 'products/'.$product_id);
    }

    /**
     * The User Profile endpoint returns information about the Uber user that
     * has authorized with the application.
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getProfile()
    {
        return $this->request('get', 'me');
    }

    /**
     * The Promotions endpoint returns information about the promotion that
     * will be available to a new user based on their activity's location.
     * These promotions do not apply for existing users.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getPromotions($attributes = [])
    {
        return $this->request('get', 'promotions', $attributes);
    }

    /**
     * Get a single request
     *
     * @param    string   $request_id    Request id
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getRequest($request_id)
    {
        return $this->request('get', 'requests/'.$request_id);
    }

    /**
     * Get a single request map
     *
     * @param    string   $request_id    Request id
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getRequestMap($request_id)
    {
        return $this->request('get', 'requests/'.$request_id.'/map');
    }

    /**
     * The Time Estimates endpoint returns ETAs for all products offered at a
     * given location, with the responses expressed as integers in seconds. We
     * recommend that this endpoint be called every minute to provide the most
     * accurate, up-to-date ETAs.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getTimeEstimates($attributes = [])
    {
        return $this->request('get', 'estimates/time', $attributes);
    }

    /**
     * The Request endpoint allows a ride to be requested on behalf of an Uber
     * user given their desired product, start, and end locations.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function requestRide($attributes = [])
    {
        return $this->request('post', 'requests', $attributes);
    }

    /**
     * Get headers for request
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => $this->getAuthorizationHeader(),
            'Accept-Language' => $this->locale,
        ];
    }

    /**
     * Build url
     *
     * @param  string   $path
     *
     * @return string   Url
     */
    public function getUrlFromPath($path)
    {
        $path = ltrim($path, '/');

        if ($this->use_sandbox) {
            return 'https://sandbox-api.uber.com/'.$this->version.'/'.$path;
        }

        return 'https://api.uber.com/'.$this->version.'/'.$path;
    }

    /**
     * Set Http Client
     *
     * @param HttpClient  $client
     *
     * @return Client
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->http_client = $client;
        return $this;
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
            'locale'    => 'en_US',
        );

        return array_merge($defaults, $configuration);
    }

    /**
     * Get authorization header value
     *
     * @return string
     */
    private function getAuthorizationHeader()
    {
        if ($this->access_token) {
            return 'Bearer '.$this->access_token;
        }

        return 'Token '.$this->server_token;
    }

    /**
     * Makes a request to the Uber API and returns the response
     *
     * @param    string $verb       The Http verb to use
     * @param    string $path       The path of the APi after the domain
     * @param    array  $parameters Parameters
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    private function request($verb, $path, $parameters = [])
    {
        $client = $this->http_client;
        $url = $this->getUrlFromPath($path);
        $verb = strtolower($verb);
        $config = $this->getConfigForVerbAndParameters($verb, $parameters);

        try {
            $response = $client->$verb($url, $config);
        } catch (HttpClientException $e) {
            throw new Exception($e->getMessage());
        }

        $this->rate_limit = new RateLimit(
            $response->getHeader('X-Rate-Limit-Limit'),
            $response->getHeader('X-Rate-Limit-Remaining'),
            $response->getHeader('X-Rate-Limit-Reset')
        );

        return json_decode($response->getBody());
    }

    /**
     * Get HttpClient config for verb and parameters
     *
     * @param  string $verb
     * @param  array  $parameters
     *
     * @return array
     */
    private function getConfigForVerbAndParameters($verb, $parameters = [])
    {
        $config = [
            'headers' => $this->getHeaders()
        ];

        if (!empty($parameters)) {
            if (strtolower($verb) == 'get') {
                $config['query'] = $parameters;
            } else {
                $config['json'] = $parameters;
            }
        }

        return $config;
    }
}
