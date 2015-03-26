<?php namespace Stevenmaguire\Uber;

trait GetSetTrait
{
    /**
     * Magic method to handle getter and setter methods
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $parameters)
    {
        if ($this->isGetMethod($method)) {
            $property = $this->convertMethodToProperty($method);

            if (property_exists($this, $property)) {
                return $this->$property;
            }
        } elseif ($this->isSetMethod($method)) {
            $property = $this->convertMethodToProperty($method);

            return $this->updateAttribute($property, $parameters[0]);
        } // @codeCoverageIgnore

        throw new Exception('Method not implemented');
    }

    /**
     * Update object attribute
     *
     * @param  string $attribute
     * @param  string|boolean|integer $value
     *
     * @return object
     */
    private function updateAttribute($attribute, $value)
    {
        if (property_exists($this, $attribute)) {
            $this->$attribute = $value;
        }

        return $this;
    }

    /**
     * Attempt to parse a method name and format its related property name
     *
     * @param  string $method
     *
     * @return string
     */
    public function convertMethodToProperty($method)
    {
        $property = preg_replace("/[g|s]{1}et(.*)/", "$1", $method);
        return strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.'_', $property));
    }

    /**
     * Checks if given method name is a valid getter method
     *
     * @param  string $method
     *
     * @return boolean
     */
    public function isGetMethod($method)
    {
        return preg_match("/^get[A-Za-z]+$/", $method);
    }

    /**
     * Checks if given method name is a valid setter method
     *
     * @param  string $method
     *
     * @return boolean
     */
    public function isSetMethod($method)
    {
        return preg_match("/^set[A-Za-z]+$/", $method);
    }
}
