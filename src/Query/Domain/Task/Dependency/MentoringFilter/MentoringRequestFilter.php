<?php

namespace Query\Domain\Task\Dependency\MentoringFilter;

class MentoringRequestFilter
{

    /**
     * 
     * @var int[]
     */
    protected $requestStatusList = [];

    /**
     * 
     * @var bool|null
     */
    protected $reportCompletedStatus;

    public function getRequestStatusList(): array
    {
        return $this->requestStatusList;
    }

    public function getReportCompletedStatus(): ?bool
    {
        return $this->reportCompletedStatus;
    }

    public function __construct()
    {
        
    }

    public function addRequestStatus(int $requestStatus)
    {
        $this->requestStatusList[] = $requestStatus;
        return $this;
    }

    public function setReportCompletedStatus(?bool $reportCompletedStatus)
    {
        $this->reportCompletedStatus = $reportCompletedStatus;
        return $this;
    }

    public function getSqlRequestStatusClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->requestStatusList)) {
            return null;
        }
        $statuses = '';
        foreach ($this->requestStatusList as $key => $requestStatus) {
            $statuses .= empty($statuses) ? ":status$key" : ", :status$key";
            $parameters["status$key"] = $requestStatus;
        }
        return "AND $tableName.requestStatus IN ($statuses)";
    }

    public function getSqlReportCompletedStatusClause(string $reportColumnName): ?string
    {
        if ($this->reportCompletedStatus === true) {
            return "AND $reportColumnName IS NOT NULL";
        } elseif ($this->reportCompletedStatus === false) {
            return "AND $reportColumnName IS NULL";
        }
        return null;
    }

}
