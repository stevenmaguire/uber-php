<?php namespace Stevenmaguire\Uber;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;

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
        $this->applyConfiguration($configuration);
        $this->http_client = new HttpClient;
    }

    /**
     * Apply configuration
     *
     * @param  array $configuration
     *
     * @return void
     */
    private function applyConfiguration($configuration = [])
    {
        array_walk($configuration, function ($value, $key) {
            $this->updateAttribute($key, $value);
        });
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

    /**
     * Get headers for request
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => trim($this->getAuthorizationHeader()),
            'Accept-Language' => trim($this->locale),
        ];
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
     * The Payment Methods endpoint allows retrieving the list of the user’s
     * available payment methods. These can be leveraged in order to supply a
     * payment_method_id to the POST /requests endpoint.
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getPaymentMethods()
    {
        return $this->request('get', 'payment-methods');
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
     * The Request Estimate endpoint allows a ride to be estimated given the
     * desired product, start, and end locations. If the end location is
     * not provided, only the pickup ETA and details of surge pricing
     * information are provided. If the pickup ETA is null, there are no cars
     * available, but an estimate may still be given to the user.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getRequestEstimate($attributes = [])
    {
        return $this->request('post', 'requests/estimate', $attributes);
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
     * Get the receipt information of the completed request.
     *
     * @param    string   $request_id    Request id
     *
     * @return   stdClass               The JSON response from the request
     */
    public function getRequestReceipt($request_id)
    {
        return $this->request('get', 'requests/'.$request_id.'/receipt');
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
     * Build url
     *
     * @param  string   $path
     *
     * @return string   Url
     */
    public function getUrlFromPath($path)
    {
        $path = ltrim($path, '/');

        $host = 'https://'.($this->use_sandbox ? 'sandbox-' : '').'api.uber.com';

        return $host.($this->version ? '/'.$this->version : '').'/'.$path;
    }

    /**
     * Handle http client exceptions
     *
     * @param  HttpClientException $e
     *
     * @return void
     * @throws Exception
     */
    private function handleRequestException(HttpClientException $e)
    {
        if ($response = $e->getResponse()) {
            $exception = new Exception($response->getReasonPhrase(), $response->getStatusCode(), $e);
            $exception->setBody(json_decode($response->getBody()));

            throw $exception;
        }

        throw new Exception($e->getMessage(), 500, $e);
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
            'version'   =>  'v1.2',
            'locale'    => 'en_US',
        );

        return array_merge($defaults, $configuration);
    }

    /**
     * Attempt to pull rate limit headers from response and add to client
     *
     * @param  Response  $response
     *
     * @return void
     */
    private function parseRateLimitFromResponse(Response $response)
    {
        $rateLimitHeaders = array_filter([
            $response->getHeader('X-Rate-Limit-Limit'),
            $response->getHeader('X-Rate-Limit-Remaining'),
            $response->getHeader('X-Rate-Limit-Reset')
        ]);

        if (count($rateLimitHeaders) == 3) {
            $rateLimitClass = new ReflectionClass(RateLimit::class);
            $this->rate_limit = $rateLimitClass->newInstanceArgs($rateLimitHeaders);
        }
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
            $this->handleRequestException($e);
        }

        $this->parseRateLimitFromResponse($response);

        return json_decode($response->getBody());
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
     * Set product properties for sandbox responses
     *
     * @param string $product_id
     * @param array $attributes
     *
     * @return  stdClass
     */
    public function setProduct($product_id, $attributes = [])
    {
        return $this->request('put', 'sandbox/products/'.$product_id, $attributes);
    }

    /**
     * Set profile properties
     *
     * @param array $attributes
     *
     * @return  stdClass
     */
    public function setProfile($attributes = [])
    {
        return $this->request('put', 'me', $attributes);
    }

    /**
     * Set request properties for sandbox responses
     *
     * @param string $request_id
     * @param array $attributes
     *
     * @return  stdClass
     */
    public function setRequest($request_id, $attributes = [])
    {
        return $this->request('put', 'sandbox/requests/'.$request_id, $attributes);
    }
}
