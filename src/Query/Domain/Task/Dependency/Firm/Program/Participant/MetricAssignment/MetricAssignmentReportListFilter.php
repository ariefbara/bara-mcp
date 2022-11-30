<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\MetricAssignment;

use Resources\PaginationFilter;

class MetricAssignmentReportListFilter
{

    const APPROVED_STATUS = 'approved';
    const REJECTED_STATUS = 'rejected';
    const UNREVIEWED_STATUS = 'unreviewed';
    const OBSERVATION_TIME_DESC = 'observation-desc';
    const OBSERVATION_TIME_ASC = 'observation-asc';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $reviewStatus;

    /**
     * 
     * @var string|null
     */
    protected $order;

    public function setReviewStatus(?string $reviewStatus)
    {
        $this->reviewStatus = $reviewStatus;
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
    
    protected function getReviewStatusCriteria(): ?string
    {
        switch ($this->reviewStatus) {
            case self::APPROVED_STATUS:
                return <<<_STATEMENT
    AND MetricAssignmentReport.approved = true
_STATEMENT;
            case self::REJECTED_STATUS:
                return <<<_STATEMENT
    AND MetricAssignmentReport.approved = false
_STATEMENT;
            case self::UNREVIEWED_STATUS:
                return <<<_STATEMENT
    AND MetricAssignmentReport.approved IS NULL
_STATEMENT;
            default:
                return null;
        }
    }
    
    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->getReviewStatusCriteria();
    }
    
    public function getOrderStatement(): string
    {
        switch ($this->order) {
            case self::OBSERVATION_TIME_ASC:
                return "ORDER BY submitTime ASC";
            default:
                return "ORDER BY submitTime DESC";
        }
    }
    
    public function getLimitStatement(): string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
