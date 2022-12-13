<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use DateTimeImmutable;
use Resources\PaginationFilter;
use Resources\QueryOrder;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;

class TaskListFilter
{

    const CONSULTANT = 'CONSULTANT';
    const COORDINATOR = 'COORDINATOR';
    //
    const MODIFIED_TIME_ASC = 'modified-time-asc';
    const MODIFIED_TIME_DESC = 'modified-time-desc';
    const DUE_DATE_ASC = 'due-date-asc';
    const DUE_DATE_DESC = 'due-date-desc';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var bool|null
     */
    protected $cancelledStatus;

    /**
     * 
     * @var bool|null
     */
    protected $completedStatus;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $modifiedTimeFrom;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $modifiedTimeTo;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $dueDateFrom;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $dueDateTo;

    /**
     * 
     * @var string|null
     */
    protected $keyword;

    /**
     * 
     * @var string|null
     */
    protected $taskSource;
    
    /**
     * 
     * @var string|null
     */
    protected $order;

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function setCompletedStatus(?bool $completedStatus)
    {
        $this->completedStatus = $completedStatus;
        return $this;
    }

    public function setModifiedTimeFrom(?DateTimeImmutable $modifiedTimeFrom)
    {
        $this->modifiedTimeFrom = $modifiedTimeFrom;
        return $this;
    }

    public function setModifiedTimeTo(?DateTimeImmutable $modifiedTimeTo)
    {
        $this->modifiedTimeTo = $modifiedTimeTo;
        return $this;
    }

    public function setDueDateFrom(?DateTimeImmutable $dueDateFrom)
    {
        $this->dueDateFrom = $dueDateFrom;
        return $this;
    }

    public function setDueDateTo(?DateTimeImmutable $dueDateTo)
    {
        $this->dueDateTo = $dueDateTo;
        return $this;
    }

    public function setKeyword(?string $keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function setTaskSource(?string $taskSource)
    {
        $this->taskSource = $taskSource;
        return $this;
    }

    public function setOrder(?string $order)
    {
        $validOrder = [
            self::MODIFIED_TIME_ASC,
            self::MODIFIED_TIME_DESC,
            self::DUE_DATE_ASC,
            self::DUE_DATE_DESC,
        ];
        if (in_array($order, $validOrder)) {
            $this->order = $order;
        }
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

    protected function getCancelledStatusOptionalStatement(&$parameters): ?string
    {
        if (is_null($this->cancelledStatus)) {
            return null;
        }
        $parameters['cancelledStatus'] = $this->cancelledStatus;
        return <<<_STATEMENT
    AND Task.cancelled = :cancelledStatus
_STATEMENT;
    }

    protected function getCompletedStatusOptionalStatement(): ?string
    {
        $approvedTaskReport = TaskReportReviewStatus::APPROVED;
        $ongoingTaskReport = implode(", ",
                [TaskReportReviewStatus::UNREVIEWED, TaskReportReviewStatus::REVISION_REQUIRED]);
        if (is_null($this->completedStatus)) {
            return null;
        }
        if ($this->completedStatus) {
            return <<<_STATEMENT
    AND TaskReport.reviewStatus = {$approvedTaskReport}
_STATEMENT;
        } else {
            return <<<_STATEMENT
    AND (
        TaskReport.id IS NULL
        OR TaskReport.reviewStatus IN ({$ongoingTaskReport})
    )
_STATEMENT;
        }
    }

    protected function getModifiedTimeFromOptionalStatement(&$parameters): ?string
    {
        if (empty($this->modifiedTimeFrom)) {
            return null;
        }
        $parameters['modifiedTimeFrom'] = $this->modifiedTimeFrom->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.modifiedTime >= :modifiedTimeFrom
_STATEMENT;
    }

    protected function getModifiedTimeToOptionalStatement(&$parameters): ?string
    {
        if (empty($this->modifiedTimeTo)) {
            return null;
        }
        $parameters['modifiedTimeTo'] = $this->modifiedTimeTo->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.modifiedTime <= :modifiedTimeTo
_STATEMENT;
    }

    protected function getDueDateFromOptionalStatement(&$parameters): ?string
    {
        if (empty($this->dueDateFrom)) {
            return null;
        }
        $parameters['dueDateFrom'] = $this->dueDateFrom->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.dueDate >= :dueDateFrom
_STATEMENT;
    }

    protected function getDueDateToOptionalStatement(&$parameters): ?string
    {
        if (empty($this->dueDateTo)) {
            return null;
        }
        $parameters['dueDateTo'] = $this->dueDateTo->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.dueDate <= :dueDateTo
_STATEMENT;
    }

    protected function getKeywordOptionalStatement(&$parameters): ?string
    {
        if (empty($this->keyword)) {
            return null;
        }
        $parameters['keyword'] = "%{$this->keyword}%";
        return <<<_STATEMENT
    AND (Task.name LIKE :keyword OR Task.description LIKE :keyword)
_STATEMENT;
    }

    protected function getTaskSourceOptionalStatement(): ?string
    {
        if ($this->taskSource == self::CONSULTANT) {
            return <<<_STATEMENT
    AND ConsultantTask.id IS NOT NULL
_STATEMENT;
        }
        if ($this->taskSource == self::COORDINATOR) {
            return <<<_STATEMENT
    AND CoordinatorTask.id IS NOT NULL
_STATEMENT;
        }
        return null;
    }

    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->getCancelledStatusOptionalStatement($parameters)
                . $this->getCompletedStatusOptionalStatement()
                . $this->getModifiedTimeFromOptionalStatement($parameters)
                . $this->getModifiedTimeToOptionalStatement($parameters)
                . $this->getDueDateFromOptionalStatement($parameters)
                . $this->getDueDateToOptionalStatement($parameters)
                . $this->getKeywordOptionalStatement($parameters)
                . $this->getTaskSourceOptionalStatement();
    }

    public function getOrderStatement(): ?string
    {
        switch ($this->order) {
            case self::MODIFIED_TIME_ASC:
                return "ORDER BY Task.modifiedTime ASC";
            case self::MODIFIED_TIME_DESC:
                return "ORDER BY Task.modifiedTime DESC";
            case self::DUE_DATE_DESC:
                return "ORDER BY Task.dueDate DESC";
            default:
                return "ORDER BY Task.dueDate ASC";
        }
    }

    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
