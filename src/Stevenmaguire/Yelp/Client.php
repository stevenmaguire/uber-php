<?php namespace Stevenmaguire\Yelp;

use Stevenmaguire\Oauth\OAuthToken;
use Stevenmaguire\Oauth\OAuthConsumer;
use Stevenmaguire\Oauth\OAuthSignatureMethodHmacSha1;
use Stevenmaguire\Oauth\OAuthRequest;

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

        return $this->request($this->api_host, $search_path);
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

        return $this->request($this->api_host, $business_path);
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
     * @param    string $host    The domain host of the API
     * @param    string $path    The path of the APi after the domain
     *
     * @return   stdClass The JSON response from the request
     */
    private function request($host, $path)
    {
        $unsigned_url = $this->buildUnsignedUrl($host, $path);
        $signed_url = $this->buildSignedUrl($unsigned_url);

        $ch = curl_init($signed_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->parseHttpResponse($http_status, $data);
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

    /**
     * Build signed url
     *
     * @param  string   $unsigned_url
     *
     * @return string   Signed url
     */
    private function buildSignedUrl($unsigned_url)
    {
        $token = $this->buildtoken();
        $consumer = $this->buildConsumer();
        $signature_method = $this->getSignature();

        $oauthrequest = OAuthRequest::from_consumer_and_token(
            $consumer,
            $token,
            'GET',
            $unsigned_url
        );

        $oauthrequest->sign_request($signature_method, $consumer, $token);

        return $oauthrequest->to_url();
    }

    /**
     * Token object built using the OAuth library
     *
     * @return OAuthToken
     */
    private function buildToken()
    {
        return new OAuthToken($this->token, $this->token_secret);
    }

    /**
     * Consumer object built using the OAuth library
     *
     * @return OAuthConsumer
     */
    private function buildConsumer()
    {
        return new OAuthConsumer($this->consumer_key, $this->consumer_secret);
    }

    /**
     * Yelp uses HMAC SHA1 encoding
     *
     * @return OAuthSignatureMethodHmacSha1
     */
    private function getSignature()
    {
        return new OAuthSignatureMethodHmacSha1();
    }

    /**
     * Parse response body for problematic status codes
     *
     * @param  string $status
     * @param  string $body
     *
     * @return stdClass
     */
    private function parseHttpResponse($status, $body)
    {
        $body = json_decode($body);
        if ($status === 200) {
            return $body;
        }
        throw new Exception($body->error->text);
    } // @codeCoverageIgnore
}
