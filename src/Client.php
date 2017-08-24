<?php namespace Stevenmaguire\Uber;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;

class Client
{
    use GetSetTrait;
    use Resources\Drivers;
    use Resources\Estimates;
    use Resources\Products;
    use Resources\Promotions;
    use Resources\Reminders;
    use Resources\Requests;
    use Resources\Riders;

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
    private $httpClient;

    /**
     * Creates a new client.
     *
     * @param    array    $configuration
     */
    public function __construct($configuration = [])
    {
        $configuration = $this->parseConfiguration($configuration);
        $this->applyConfiguration($configuration);
        $this->httpClient = new HttpClient;
    }

    /**
     * Applies configuration to client.
     *
     * @param   array     $configuration
     *
     * @return  void
     */
    private function applyConfiguration($configuration = [])
    {
        array_walk($configuration, function ($value, $key) {
            $this->updateAttribute($key, $value);
        });
    }

    /**
     * Gets authorization header value.
     *
     * @return   string
     */
    private function getAuthorizationHeader()
    {
        if ($this->access_token) {
            return 'Bearer '.$this->access_token;
        }

        return 'Token '.$this->server_token;
    }

    /**
     * Gets HttpClient config for verb and parameters.
     *
     * @param    string   $verb
     * @param    array    $parameters
     *
     * @return   array
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
     * Gets headers for request.
     *
     * @return   array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => trim($this->getAuthorizationHeader()),
            'Accept-Language' => trim($this->locale),
        ];
    }

    /**
     * Builds url from path.
     *
     * @param    string   $path
     *
     * @return   string   Url
     */
    public function getUrlFromPath($path)
    {
        $path = ltrim($path, '/');

        $host = 'https://'.($this->use_sandbox ? 'sandbox-' : '').'api.uber.com';

        return $host.($this->version ? '/'.$this->version : '').'/'.$path;
    }

    /**
     * Handles http client exceptions.
     *
     * @param    HttpClientException $e
     *
     * @return   void
     * @throws   Exception
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
     * Parses configuration using defaults.
     *
     * @param    array    $configuration
     *
     * @return   array    $configuration
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
     * Attempts to pull rate limit headers from response and add to client.
     *
     * @param    Response $response
     *
     * @return   void
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
     * Makes a request to the Uber API and returns the response.
     *
     * @param    string   $verb       The Http verb to use
     * @param    string   $path       The path of the APi after the domain
     * @param    array    $parameters Parameters
     *
     * @return   stdClass             The JSON response from the request
     * @throws   Exception
     */
    protected function request($verb, $path, $parameters = [])
    {
        $client = $this->httpClient;
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
     * Sets Http Client.
     *
     * @param    HttpClient  $client
     *
     * @return   Client
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * Throws exception when client is not configured sandbox use. Should only
     * be utilized when attempting to do work against ephemeral sandbox API
     * data.
     *
     * @return   void
     * @throws   Exception
     *
     * @see      https://developer.uber.com/docs/riders/guides/sandbox
     */
    private function enforceSandboxExpectation($message = null)
    {
        if (!$this->use_sandbox) {
            $message = $message ?: 'Attempted to invoke sandbox functionality '.
                'with production client; this is not recommended';
            throw new Exception($message);
        }
    }
}
