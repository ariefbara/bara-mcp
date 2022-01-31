<?php

namespace Query\Domain\Task\Dependency\MentoringFilter;

class DeclaredMentoringFilter
{

    /**
     * 
     * @var int[]
     */
    protected $declaredStatusList;

    /**
     * 
     * @var bool|null
     */
    protected $reportCompletedStatus;

    public function __construct()
    {
        
    }

    public function addDeclaredStatus(int $declaredStatus)
    {
        $this->declaredStatusList[] = $declaredStatus;
        return $this;
    }

    public function setReportCompletedStatus(?bool $reportCompletedStatus)
    {
        $this->reportCompletedStatus = $reportCompletedStatus;
        return $this;
    }
    
    public function getSqlRequestStatusClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->declaredStatusList)) {
            return null;
        }
        $statuses = '';
        foreach ($this->declaredStatusList as $key => $declaredStatus) {
            $statuses .= empty($statuses) ? ":status$key" : ", :status$key";
            $parameters["status$key"] = $declaredStatus;
        }
        return "AND $tableName.declaredStatus IN ($statuses)";
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
