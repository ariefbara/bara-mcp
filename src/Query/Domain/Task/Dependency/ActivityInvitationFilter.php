<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

class ActivityInvitationFilter
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

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
    protected $orderDirection = 'DESC';

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

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

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
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

    public function setOrderDirection(?string $orderDirection)
    {
        if (isset($orderDirection)) {
            $c = new ReflectionClass($this);
            if (!in_array($orderDirection, $c->getConstants())) {
                throw RegularException::badRequest('bad request: invalid order argument');
            }
            $this->orderDirection = $orderDirection;
        }
        return $this;
    }

}
