<?php namespace Stevenmaguire\Uber\Resources;

trait Promotions
{
    /**
     * Lists available promotions.
     *
     * The Promotions endpoint returns information about the promotion that
     * will be available to a new user based on their activity's location.
     * These promotions do not apply for existing users.
     *
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/ride-promotions/introduction
     */
    public function getPromotions($attributes = [])
    {
        return $this->request('get', 'promotions', $attributes);
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
}
