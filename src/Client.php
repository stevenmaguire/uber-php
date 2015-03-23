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
     * Get Http client for making requests, and helping tests!
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return new HttpClient;
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
        $client = $this->getHttpClient();
        $url = $this->buildUrl($path);

        try {
            $request = $client->createRequest($verb, $url, [
                'headers' => [
                    'Authorization' => $this->getAuthorizationHeader(),
                    'Accept-Language' => $this->locale,
                ],
                'query' => $parameters,
            ]);
            $response = $client->send($request);
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
     * Build url
     *
     * @param  string   $path
     *
     * @return string   Url
     */
    private function buildUrl($path)
    {
        $path = ltrim($path, '/');

        if ($this->use_sandbox) {
            return 'https://sandbox-api.uber.com/'.$this->version.'/sandbox/'.$path;
        }

        return 'https://api.uber.com/'.$this->version.'/'.$path;
    }
}
