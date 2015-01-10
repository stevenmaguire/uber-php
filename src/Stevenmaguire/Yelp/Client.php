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
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->consumer_key = $config['consumer_key'];
        $this->consumer_secret = $config['consumer_secret'];
        $this->token = $config['token'];
        $this->token_secret = $config['token_secret'];
        $this->api_host = $config['api_host'];
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
     * Makes a request to the Yelp API and returns the response
     *
     * @param    $host    The domain host of the API
     * @param    $path    The path of the APi after the domain
     *
     * @return   The JSON response from the request
     */
    private function request($host, $path)
    {
        $unsigned_url = "http://" . $host . $path;

        // Token object built using the OAuth library
        $token = new OAuthToken($this->token, $this->token_secret);

        // Consumer object built using the OAuth library
        $consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);

        // Yelp uses HMAC SHA1 encoding
        $signature_method = new OAuthSignatureMethodHmacSha1();

        $oauthrequest = OAuthRequest::from_consumer_and_token(
            $consumer,
            $token,
            'GET',
            $unsigned_url
        );

        // Sign the request
        $oauthrequest->sign_request($signature_method, $consumer, $token);

        // Get the signed URL
        $signed_url = $oauthrequest->to_url();

        // Send Yelp API Call
        $ch = curl_init($signed_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data);
    }

    /**
     * Query the Search API by a search term and location
     *
     * @param    $term        The search term passed to the API
     * @param    $location    The search location passed to the API
     *
     * @return   stdClass     The JSON response from the request
     */
    public function search($term = null, $location = null)
    {
        $url_params = array();

        $url_params['term'] = $term ?: $this->default_term;
        $url_params['location'] = $location?: $this->default_location;
        $url_params['limit'] = $this->search_limit;
        $search_path = $this->search_path . "?" . http_build_query($url_params);

        return $this->request($this->api_host, $search_path);
    }

    /**
     * Query the Business API by business_id
     *
     * @param    $business_id    The ID of the business to query
     *
     * @return   stdClass        The JSON response from the request
     */
    public function getBusiness($business_id)
    {
        $business_path = $this->business_path . $business_id;

        return $this->request($this->api_host, $business_path);
    }
}
