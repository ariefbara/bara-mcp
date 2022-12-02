<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use Resources\PaginationFilter;

class MentoringListFilter
{
    const ORDER_BY_START_TIME_DESC = 'start-time-desc';
    const ORDER_BY_START_TIME_ASC = 'start-time-asc';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

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
    protected $order;

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

    public function setOrder(?string $order)
    {
        $this->order = $order;
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }
    
    protected function getFromCriteria(&$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND _mentoring.startTime >= :from
_STATEMENT;
    }
    protected function getToCriteria(&$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND _mentoring.endTime <= :to
_STATEMENT;
    }
    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->getFromCriteria($parameters)
                . $this->getToCriteria($parameters);
    }
    
    public function getOrderStatement(): ?string
    {
        switch ($this->order) {
            case self::ORDER_BY_START_TIME_ASC:
                return "ORDER BY startTime ASC";
            default:
                return "ORDER BY startTime DESC";
        }
    }
    
    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
