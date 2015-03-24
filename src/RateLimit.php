<?php namespace Stevenmaguire\Uber;

class RateLimit
{
    use GetSetTrait;

    /**
     * Rate limit upper limit
     *
     * @var string
     */
    private $limit;

    /**
     * Rate limit remaining
     *
     * @var string
     */
    private $remaining;

    /**
     * Rate limit reset timestamp
     *
     * @var string
     */
    private $reset;

    /**
     * Create new RateLimit
     *
     * @param string $limit
     * @param string $remaining
     * @param string $reset
     */
    public function __construct($limit, $remaining, $reset)
    {
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->reset = $reset;
    }
}
