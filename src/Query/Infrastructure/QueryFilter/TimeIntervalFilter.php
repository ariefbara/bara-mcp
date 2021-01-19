<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;

class TimeIntervalFilter
{

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $from;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $to;

    public function setFrom(?DateTimeImmutable $from)
    {
        $this->from = $from;
        return $this;
    }

    public function setTo(?DateTimeImmutable $to)
    {
        $this->to = $to;
        return $this;
    }

    public function __construct()
    {
        
    }

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

}
