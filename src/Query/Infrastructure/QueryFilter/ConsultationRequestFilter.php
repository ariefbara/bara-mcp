<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;

class ConsultationRequestFilter
{

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $minStartTime;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $maxEndTime;

    /**
     *
     * @var bool|null
     */
    protected $concludedStatus;

    /**
     *
     * @var array|null
     */
    protected $status = [];

    public function setMinStartTime(?DateTimeImmutable $minStartTime)
    {
        $this->minStartTime = $minStartTime;
        return $this;
    }

    public function setMaxEndTime(?DateTimeImmutable $maxEndTime)
    {
        $this->maxEndTime = $maxEndTime;
        return $this;
    }

    public function setConcludedStatus(?bool $concludedStatus)
    {
        $this->concludedStatus = $concludedStatus;
        return $this;
    }

    public function setStatus(?array $status)
    {
        $this->status = $status;
        return $this;
    }

    public function __construct()
    {
        ;
    }

    public function getMinStartTime(): ?DateTimeImmutable
    {
        return $this->minStartTime;
    }

    public function getMaxEndTime(): ?DateTimeImmutable
    {
        return $this->maxEndTime;
    }

    public function getConcludedStatus(): ?bool
    {
        return $this->concludedStatus;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }

}
