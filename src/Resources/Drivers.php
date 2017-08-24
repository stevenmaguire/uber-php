<?php namespace Stevenmaguire\Uber\Resources;

trait Drivers
{
    /**
     * Fetches the profile for the current driver.
     *
     * The Profile endpoint returns the profile of the authenticated driver.
     * A profile includes information such as name, email, rating, and activation status.
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/drivers/references/api/v1/partners-me-get
     */
    public function getDriverProfile()
    {
        return $this->request('get', 'partners/me');
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
     * Lists payments for the current driver.
     *
     * The Earnings endpoint returns an array of payments for the given driver.
     * Payments are available at this endpoint in near real-time. Some entries,
     * such as device_subscription will appear on a periodic basis when actually
     * billed to the partner.
     *
     * If a trip is cancelled (either by rider or driver) and there is no payment
     * made, the corresponding trip_id of that cancelled trip will not appear in
     * this endpoint. If the given driver works for a fleet manager, there will
     * be no payments associated and the response will always be an empty array.
     * Drivers working for fleet managers will receive payments from the fleet
     * manager and not from Uber.
     *
     * @param array $attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/drivers/references/api/v1/partners-payments-get
     */
    public function getDriverPayments($attributes = [])
    {
        return $this->request('get', '/partners/payments', $attributes);
    }

    /**
     * Lists trips for the current driver.
     *
     * The Trip History endpoint returns an array of trips for the authenticated
     * driver.
     *
     * @param array $attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/drivers/references/api/v1/partners-trips-get
     */
    public function getDriverTrips($attributes = [])
    {
        return $this->request('get', '/partners/trips', $attributes);
    }
}
