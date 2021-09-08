<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\IEvaluationReportSummaryResult;

class GenerateProgramEvaluationReportSummaryTask implements ITaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var EvaluationReportSummaryFilter
     */
    protected $evaluationReportFilter;

    /**
     * 
     * @var IProgramEvaluationReportSummaryResult
     */
    protected $programEvaluationReportSummaryResult;

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository,
            EvaluationReportSummaryFilter $evaluationReportFilter,
            IProgramEvaluationReportSummaryResult $programEvaluationReportSummaryResult)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->evaluationReportFilter = $evaluationReportFilter;
        $this->programEvaluationReportSummaryResult = $programEvaluationReportSummaryResult;
    }

    public function executeTaskInProgram(Program $program): void
    {
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedEvaluationReportsInProgram($program, $this->evaluationReportFilter);
        
        foreach ($evaluationReports as $evaluationReport) {
            $this->programEvaluationReportSummaryResult->includeEvaluationReport($evaluationReport);
        }
    }

}
