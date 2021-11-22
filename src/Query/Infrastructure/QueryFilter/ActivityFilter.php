<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

class ActivityFilter
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
     * @var string|null
     */
    protected $order;

    /**
     * 
     * @var string[]
     */
    protected $activityTypeIdList = [];

    /**
     * 
     * @var string[]
     */
    protected $initiatorTypeList = [];

    const ASCENDING = 'ASC';
    const DESCENDING = 'DESC';

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

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getActivityTypeIdList(): array
    {
        return $this->activityTypeIdList;
    }

    public function getInitiatorTypeList(): array
    {
        return $this->initiatorTypeList;
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

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function setOrder(?string $order)
    {
        $c = new ReflectionClass($this);
        if (!is_null($order) && !in_array($order, $c->getConstants())) {
            throw RegularException::badRequest("bad request: list order can only be 'ASC' or 'DESC'");
        }
        $this->order = $order;
        return $this;
    }

    public function addActivityTypeId(string $activityTypeId): self
    {
        $this->activityTypeIdList[] = $activityTypeId;
        return $this;
    }
    
    public function addInitiatorTypeList(string $userType): self
    {
        $this->initiatorTypeList[] = $userType;
        return $this;
    }

}
