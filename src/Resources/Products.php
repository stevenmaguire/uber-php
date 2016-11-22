<?php namespace Stevenmaguire\Uber\Resources;

trait Products
{
    /**
     * Fetches a specific product.
     *
     * @param    string   $productId    Product id
     *
     * @return   stdClass               The JSON response from the request
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/products-product_id-get
     */
    public function getProduct($productId)
    {
        return $this->request('get', 'products/'.$productId);
    }

    /**
     * Lists available products.
     *
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
     *
     * @see      https://developer.uber.com/docs/riders/references/api/v1.2/products-get
     */
    public function getProducts($attributes = [])
    {
        return $this->request('get', 'products', $attributes);
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
     * Updates a specific product properties for sandbox responses.
     *
     * @param    string   $productId    Product id
     * @param    array    $attributes   Query attributes
     *
     * @return   stdClass               The JSON response from the request
     * @throws   Exception
     *
     * @see      https://developer.uber.com/docs/riders/guides/sandbox#product-types
     */
    public function setSandboxProduct($productId, $attributes = [])
    {
        $this->enforceSandboxExpectation();

        return $this->request('put', 'sandbox/products/'.$productId, $attributes);
    }
}
