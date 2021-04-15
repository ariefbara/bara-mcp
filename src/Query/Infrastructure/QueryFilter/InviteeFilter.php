<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;

class InviteeFilter
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

    /**
     * 
     * @var bool|null
     */
    protected $cancelledStatus;

    /**
     * 
     * @var array|null
     */
    protected $willAttendStatuses;

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function getWillAttendStatuses(): ?array
    {
        return $this->willAttendStatuses;
    }

    public function __construct()
    {
        
    }

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

    public function setCancelledStatus(?bool $cancelledStatus): self
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function addWillAttendStatus(?bool $willAttendStatus): self
    {
        $this->willAttendStatuses[] = $willAttendStatus;
        return $this;
    }

}
