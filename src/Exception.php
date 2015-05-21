<?php namespace Stevenmaguire\Uber;

use \Exception as BaseException;

class Exception extends BaseException
{
    /**
     * Exception body
     *
     * @var string|array
     */
    protected $body;

    /**
     * Get exception body
     *
     * @return string|array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set exception body
     *
     * @param string|array $body
     *
     * @return $this
     */
    public function setBody($body = null)
    {
        $this->body = $body;

        return $this;
    }
}
