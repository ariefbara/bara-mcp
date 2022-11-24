<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use DateTimeImmutable;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class TaskListFilter
{

    const CONSULTANT = 'CONSULTANT';
    const COORDINATOR = 'COORDINATOR';

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
    protected $keyword;

    /**
     * 
     * @var string|null
     */
    protected $taskSource;

    /**
     * 
     * @var QueryOrder|null
     */
    protected $modifiedTimeOrder;

    /**
     * 
     * @var QueryOrder|null
     */
    protected $createdTimeOrder;

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

    public function setModifiedTimeOrder(?QueryOrder $modifiedTimeOrder)
    {
        $this->modifiedTimeOrder = $modifiedTimeOrder;
        return $this;
    }

    public function setCreatedTimeOrder(?QueryOrder $createdTimeOrder)
    {
        $this->createdTimeOrder = $createdTimeOrder;
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
        if (is_null($this->completedStatus)) {
            return null;
        }
        if ($this->completedStatus) {
            return <<<_STATEMENT
    AND TaskReport.id IS NOT NULL
_STATEMENT;
        } else {
            return <<<_STATEMENT
    AND TaskReport.id IS NULL
_STATEMENT;
        }
    }

    protected function getFromOptionalStatement(&$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.modifiedTime >= :from
_STATEMENT;
    }

    protected function getToOptionalStatement(&$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Task.modifiedTime <= :to
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
                . $this->getFromOptionalStatement($parameters)
                . $this->getToOptionalStatement($parameters)
                . $this->getKeywordOptionalStatement($parameters)
                . $this->getTaskSourceOptionalStatement();
    }

    public function getOrderStatement(): ?string
    {
        $orders = [];
        if (!empty($this->modifiedTimeOrder)) {
            $orders[] = "modifiedTime {$this->modifiedTimeOrder->getOrder()}";
        }
        if (!empty($this->createdTimeOrder)) {
            $orders[] = "createdTime {$this->createdTimeOrder->getOrder()}";
        }
        $orderStatement = implode(', ', $orders);
        return empty($orderStatement) ? '' : "ORDER BY {$orderStatement}";
    }

    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
