<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Query\Domain\Model\Firm\Program\ProgramMentorEvaluationReportSummary;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;

class GenerateProgramMentorEvaluationReportSummaryTask implements ITaskInProgramExecutableByCoordinator
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
    protected $evaluationReportSummaryFilter;

    /**
     * 
     * @var ProgramMentorEvaluationReportSummary
     */
    protected $result;

    public function getResult(): ProgramMentorEvaluationReportSummary
    {
        return $this->result;
    }

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository,
            EvaluationReportSummaryFilter $evaluationReportSummaryFilter)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->evaluationReportSummaryFilter = $evaluationReportSummaryFilter;
        $this->result = new ProgramMentorEvaluationReportSummary();
    }

    public function executeTaskInProgram(Program $program): void
    {
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedEvaluationReportsInProgram($program, $this->evaluationReportSummaryFilter);
        foreach ($evaluationReports as $evaluationReport) {
            $this->result->includeEvaluationReport($evaluationReport);
        }
    }

}
