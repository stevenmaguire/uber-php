<?php namespace Stevenmaguire\Uber\Resources;

trait Requests
{
    /**
     * Cancels the current ride request.
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-delete
     */
    public function cancelCurrentRequest()
    {
        return $this->cancelRequest('current');
    }

    /**
     * Cancels a specific ride request.
     *
     * @param    string   $requestId    Request id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-delete
     */
    public function cancelRequest($requestId)
    {
        return $this->request('delete', 'requests/'.$requestId);
    }

    /**
     * Fetches the current ride request.
     *
     * The Ride Request endpoint allows retrieving real-time details for an
     * ongoing trip.
     *
     * This endpoint behaves similarly to the GET /requests/{request_id}
     * endpoint, except you do not need to provide a request_id. If there is
     * no trip in progress the endpoint will result in a 404 not found error.
     * This endpoint will only work for trips requested through your app unless
     * you have the all_trips scope.
     *
     * By default, only details about trips your app requested will be returned.
     * If your app has all_trips scope, however, trip details will be returned
     * for all trips irrespective of which application initiated them.
     *
     * See the Ride Request tutorial for a step-by-step guide to requesting
     * rides on behalf of an Uber user. Please review the Sandbox documentation
     * on how to develop and test against these endpoints without making
     * real-world Ride Requests and being charged.
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-get
     */
    public function getCurrentRequest()
    {
        return $this->getRequest('current');
    }

    /**
     * Fetches a specific ride request.
     *
     * @param    string   $requestId    Request id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-get
     */
    public function getRequest($requestId)
    {
        return $this->request('get', 'requests/'.$requestId);
    }

    /**
     * Creates a ride request estimate.
     *
     * The Request Estimate endpoint allows a ride to be estimated given the
     * desired product, start, and end locations. If the end location is
     * not provided, only the pickup ETA and details of surge pricing
     * information are provided. If the pickup ETA is null, there are no cars
     * available, but an estimate may still be given to the user.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-estimate-post
     */
    public function getRequestEstimate($attributes = [])
    {
        return $this->request('post', 'requests/estimate', $attributes);
    }

    /**
     * Fetches the map for a specific ride request.
     *
     * @param    string   $requestId    Request id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-map-get
     */
    public function getRequestMap($requestId)
    {
        return $this->request('get', 'requests/'.$requestId.'/map');
    }

    /**
     * Fetches the receipt for a specific ride request.
     *
     * @param    string   $requestId    Request id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-receipt-get
     */
    public function getRequestReceipt($requestId)
    {
        return $this->request('get', 'requests/'.$requestId.'/receipt');
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
     * Creates a new ride request.
     *
     * The Request endpoint allows a ride to be requested on behalf of an Uber
     * user given their desired product, start, and end locations.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-post
     */
    public function requestRide($attributes = [])
    {
        return $this->request('post', 'requests', $attributes);
    }

    /**
     * Updates the current ride request.
     *
     * The Ride Request endpoint allows updating an ongoing request’s
     * destination.
     *
     * This endpoint behaves similarly to the PATCH /v1.2/requests/{request_id}
     * endpoint, except you do not need to provide a request_id. If there is no
     * trip in progress the endpoint will result in a 404 not found error. This
     * endpoint will only work for trips requested through your app unless you
     * have the all_trips scope.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-current-patch
     */
    public function setCurrentRequest($attributes = [])
    {
        return $this->setRequest('current', $attributes);
    }

    /**
     * Updates a specific ride request.
     *
     * The Ride Request endpoint allows updating an ongoing request’s
     * destination using the Ride Request endpoint.
     *
     * @param    string   $requestId    Request id
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-request_id-patch
     */
    public function setRequest($requestId, $attributes = [])
    {
        return $this->request('patch', 'requests/'.$requestId, $attributes);
    }

    /**
     * Updates a specific ride request properties for sandbox responses.
     *
     * @param    string   $requestId    Request id
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     * @throws   Exception
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/requests-estimate-post
     */
    public function setSandboxRequest($requestId, $attributes = [])
    {
        $this->enforceSandboxExpectation();

        return $this->request('put', 'sandbox/requests/'.$requestId, $attributes);
    }
}
