<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

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

    /**
     * 
     * @var string|null
     */
    protected $order;
    
    const  ASCENDING = 'ASC';
    const  DESCENDING = 'DESC';

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

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function __construct()
    {
        
    }

    public function setFrom(?DateTimeImmutable $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function setTo(?DateTimeImmutable $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function setOrder(?string $order): self
    {
        $c = new ReflectionClass($this);
        if (!is_null($order) && !in_array($order, $c->getConstants())) {
            throw RegularException::badRequest("bad request: list order can only be 'ASC' or 'DESC'");
        }
        $this->order = $order;
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
