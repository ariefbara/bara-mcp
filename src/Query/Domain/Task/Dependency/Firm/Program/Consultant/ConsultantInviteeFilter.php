<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use DateTimeImmutable;
use Resources\Domain\ValueObject\QueryOrder;

class ConsultantInviteeFilter
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
     * @var QueryOrder
     */
    protected $queryOrder;

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getQueryOrder(): QueryOrder
    {
        return $this->queryOrder;
    }

    public function __construct(QueryOrder $queryOrder)
    {
        $this->queryOrder = $queryOrder;
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

}
