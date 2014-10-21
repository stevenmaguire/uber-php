<?php namespace Stevenmaguire\Yelp;

use Stevenmaguire\Oauth\OAuthToken,
    Stevenmaguire\Oauth\OAuthConsumer,
    Stevenmaguire\Oauth\OAuthSignatureMethodHmacSha1,
    Stevenmaguire\Oauth\OAuthRequest;

class Client
{
    private $api_host;
    private $consumer_key;
    private $consumer_secret;
    private $token;
    private $token_secret;
    private $default_term = 'bar';
    private $default_location = 'Chicago, IL';
    private $search_limit = 3;
    private $search_path = '/v2/search/';
    private $business_path = '/v2/business/';

    public function __construct($config = [])
    {
        $this->consumer_key = $config['consumer_key'];
        $this->consumer_secret = $config['consumer_secret'];
        $this->token = $config['token'];
        $this->token_secret = $config['token_secret'];
        $this->api_host = $config['api_host'];
    }

    public function setDefaultLocation($location)
    {
        $this->default_location = $location;
        return $this;
    }

    public function setDefaultTerm($term)
    {
        $this->default_term = $term;
        return $this;
    }

    public function setSearchLimit($limit)
    {
        if (is_numeric($limit)) {
            $this->search_limit = $limit;
        }
        return $this;
    }

    /**
     * Makes a request to the Yelp API and returns the response
     *
     * @param    $host    The domain host of the API
     * @param    $path    The path of the APi after the domain
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

        return $data;
    }

    /**
     * Query the Search API by a search term and location
     *
     * @param    $term        The search term passed to the API
     * @param    $location    The search location passed to the API
     * @return   The JSON response from the request
     */
    public function search($term, $location)
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
     * @return   The JSON response from the request
     */
    public function get_business($business_id)
    {
        $business_path = $this->business_path . $business_id;

        return $this->request($this->api_host, $business_path);
    }
}
