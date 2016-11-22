<?php namespace Stevenmaguire\Uber;

trait GetSetTrait
{
    /**
     * Attempts to magically handle getter and setter methods.
     *
     * @param    string   $method
     * @param    array    $parameters
     *
     * @return   mixed
     * @throws   Exception
     */
    public function __call($method, $parameters)
    {
        $property = $this->convertMethodToProperty($method);

        if ($this->isGetMethod($method)) {
            return $this->getAttribute($property);
        } elseif ($this->isSetMethod($method)) {
            return $this->updateAttribute($property, $parameters[0]);
        } // @codeCoverageIgnore

        throw new Exception($method . ' method not implemented');
    }

    /**
     * Attempts to parse a method name and format its related property name.
     *
     * @param    string   $method
     *
     * @return   string
     */
    public function convertMethodToProperty($method)
    {
        $property = preg_replace("/[g|s]{1}et(.*)/", "$1", $method);

        return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.'_', $property));
    }

    /**
     * Fetches a specific attribute of the current object.
     *
     * @param    string   $attribute
     *
     * @return   mixed|null
     */
    private function getAttribute($attribute)
    {
        if (property_exists($this, $attribute)) {
            return $this->$attribute;
        }

        throw new Exception($attribute . ' attribute not defined');
    }

    /**
     * Checks if given method name is a valid getter method.
     *
     * @param    string   $method
     *
     * @return   boolean
     */
    public function isGetMethod($method)
    {
        return preg_match("/^get[A-Za-z]+$/", $method);
    }

    /**
     * Checks if given method name is a valid setter method.
     *
     * @param    string   $method
     *
     * @return   boolean
     */
    public function isSetMethod($method)
    {
        return preg_match("/^set[A-Za-z]+$/", $method);
    }

    /**
     * Updates a specific attribute of the current object.
     *
     * @param    string                   $attribute
     * @param    string|boolean|integer   $value
     *
     * @return   object
     */
    private function updateAttribute($attribute, $value)
    {
        if (property_exists($this, $attribute)) {
            $this->$attribute = $value;
        }

        return $this;
    }
}
