<?php namespace Stevenmaguire\Uber\Resources;

trait Riders
{
    /**
     * Lists history events for the current rider.
     *
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
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/history-get
     */
    public function getHistory($attributes = [])
    {
        return $this->request('get', 'history', $attributes);
    }

    /**
     * Lists available payment methods for the current rider.
     *
     * The Payment Methods endpoint allows retrieving the list of the user’s
     * available payment methods. These can be leveraged in order to supply a
     * payment_method_id to the POST /requests endpoint.
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/payment-methods-get
     */
    public function getPaymentMethods()
    {
        return $this->request('get', 'payment-methods');
    }

    /**
     * Fetches a specific place.
     *
     * The Places endpoint allows retrieving the home and work addresses from
     * an Uber user's profile.
     *
     * Only home and work are acceptable.
     *
     * @param    string   $placeId      Place id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-get
     */
    public function getPlace($placeId)
    {
        return $this->request('get', 'places/'.$placeId);
    }

    /**
     * Fetches the profile for the current rider.
     *
     * The User Profile endpoint returns information about the Uber user that
     * has authorized with the application.
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/me-get
     */
    public function getProfile()
    {
        return $this->request('get', 'me');
    }

    /**
     * Makes a request to the Uber API and returns the response.
     *
     * @param    string $verb       The Http verb to use
     * @param    string $path       The path of the APi after the domain
     * @param    array  $parameters Parameters
     *
     * @return   stdClass The JSON response from the request
     * @throws   Exception
     */
    abstract protected function request($verb, $path, $parameters = []);

    /**
     * Updates a specific place.
     *
     * The Places endpoint allows updating the home and work addresses from an
     * Uber user's profile.
     *
     * Only home and work are acceptable.
     *
     * @param    string   $placeId      Place id
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/places-place_id-put
     */
    public function setPlace($placeId, $attributes = [])
    {
        return $this->request('put', 'places/'.$placeId, $attributes);
    }

    /**
     * Updates the profile for the current rider.
     *
     * @param array $attributes
     *
     * @return   stdClass
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/me-patch
     */
    public function setProfile($attributes = [])
    {
        return $this->request('patch', 'me', $attributes);
    }
}
