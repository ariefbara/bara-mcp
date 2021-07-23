<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportFilter;

class ViewAllEvaluationReportsPayload
{

    /**
     * 
     * @var string
     */
    protected $programId;

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
     * @var EvaluationReportFilter
     */
    protected $evaluationReportFilter;

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getEvaluationReportFilter(): EvaluationReportFilter
    {
        return $this->evaluationReportFilter;
    }

    public function __construct(
            string $programId, int $page, int $pageSize, EvaluationReportFilter $evaluationReportFilter)
    {
        $this->programId = $programId;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->evaluationReportFilter = $evaluationReportFilter;
    }

}
