<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

class ScheduleFilter
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var int
     */
    protected $pageSize;

    /**
     * 
     * @var int
     */
    protected $page;

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

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function __construct(int $pageSize, int $page)
    {
        $this->pageSize = $pageSize;
        $this->page = $page;
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
        if (isset($orderDirection)) {
            $c = new ReflectionClass($this);
            if (!in_array($orderDirection, $c->getConstants())) {
                throw RegularException::badRequest('bad request: invalid order argument');
            }
            $this->orderDirection = $orderDirection;
        }
        return $this;
    }

    public function getOffset(): int
    {
        return $this->pageSize * ($this->page - 1);
    }

    public function getSqlFromClause(string $startTimeColumn, array &$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return "AND $startTimeColumn >= :from";
    }

    public function getSqlToClause(string $startTimeColumn, array &$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return "AND $startTimeColumn < :to";
    }

}
