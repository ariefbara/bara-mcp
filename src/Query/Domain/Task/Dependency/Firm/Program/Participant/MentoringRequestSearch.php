<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use DateTimeImmutable;

class MentoringRequestSearch
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
     * @var string|null
     */
    protected $orderDirection = 'ASC';

    /**
     * 
     * @var int[]
     */
    protected $requestStatusList = [];

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function getRequestStatusList(): array
    {
        return $this->requestStatusList;
    }

    public function __construct()
    {
        
    }

    public function addRequestStatus(int $requestStatus)
    {
        $this->requestStatusList[] = $requestStatus;
        return $this;
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

    public function setOrderDirection(?string $orderDirection)
    {
        if ($orderDirection === 'DESC') {
            $this->orderDirection = $orderDirection;
        }
        return $this;
    }

}
