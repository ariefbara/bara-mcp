<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Resources\PaginationFilter;

class WorksheetListFilter
{

    const SUBMIT_TIME_ASC_ORDER = 'submit-asc';
    const SUBMIT_TIME_DESC_ORDER = 'submit-desc';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var bool|null
     */
    protected $reviewedStatus;

    /**
     * 
     * @var string|null
     */
    protected $missionId;

    /**
     * 
     * @var string|null
     */
    protected $order;

    public function setReviewedStatus(?bool $reviewedStatus)
    {
        $this->reviewedStatus = $reviewedStatus;
        return $this;
    }

    public function setMissionId(?string $missionId)
    {
        $this->missionId = $missionId;
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

    protected function getReviewedStatusCriteriaStatement(): ?string
    {
        if (is_null($this->reviewedStatus)) {
            return null;
        }
        return $this->reviewedStatus ? 
            <<<_STATEMENT
    AND _consultantComment.consultantCommentCount IS NOT NULL
_STATEMENT:
            <<<_STATEMENT
    AND _consultantComment.consultantCommentCount IS NULL
_STATEMENT;
    }

    protected function getMissionIdCriteriaStatement(&$parameters): ?string
    {
        if (is_null($this->missionId)) {
            return null;
        }
        $parameters['missionId'] = $this->missionId;
        return <<<_STATEMENT
    AND Worksheet.Mission_id = :missionId
_STATEMENT;
    }

    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->getReviewedStatusCriteriaStatement()
                . $this->getMissionIdCriteriaStatement($parameters);
    }

    public function getOrderStatement(): ?string
    {
        switch ($this->order) {
            case self::SUBMIT_TIME_ASC_ORDER:
                return 'ORDER BY submitTime ASC';
            default:
                return 'ORDER BY submitTime DESC';
                break;
        }
    }

    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
