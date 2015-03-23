<?php namespace Stevenmaguire\Uber;

class RateLimit
{
    use GetSetTrait;

    public function __construct($limit, $remaining, $reset)
    {
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->reset = $reset;
    }
}
