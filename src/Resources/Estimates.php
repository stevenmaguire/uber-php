<?php namespace Stevenmaguire\Uber\Resources;

trait Estimates
{
    /**
     * Fetches a price estimate.
     *
     * The Price Estimates endpoint returns an estimated price range for each
     * product offered at a given location. The price estimate is provided as
     * a formatted string with the full price range and the localized currency
     * symbol.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/estimates-price-get
     */
    public function getPriceEstimates($attributes = [])
    {
        return $this->request('get', 'estimates/price', $attributes);
    }

    /**
     * Fetches a time estimate.
     *
     * The Time Estimates endpoint returns ETAs for all products offered at a
     * given location, with the responses expressed as integers in seconds. We
     * recommend that this endpoint be called every minute to provide the most
     * accurate, up-to-date ETAs.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/estimates-time-get
     */
    public function getTimeEstimates($attributes = [])
    {
        return $this->request('get', 'estimates/time', $attributes);
    }
}
