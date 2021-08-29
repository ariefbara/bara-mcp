<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\IEvaluationReportSummaryResult;

class GenerateFirmEvaluationReportSummaryTask implements ITaskInFirmExecutableByManager
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
     * @var IFirmEvaluationReportSummaryResult
     */
    protected $firmEvaluationReportSummaryResult;

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository,
            EvaluationReportSummaryFilter $evaluationReportSummaryFilter,
            IFirmEvaluationReportSummaryResult $firmEvaluationReportSummaryResult)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->evaluationReportSummaryFilter = $evaluationReportSummaryFilter;
        $this->firmEvaluationReportSummaryResult = $firmEvaluationReportSummaryResult;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedEvaluationReportsInFirm($firm, $this->evaluationReportSummaryFilter);
        foreach ($evaluationReports as $evaluationReport) {
            $this->firmEvaluationReportSummaryResult->includeEvaluationReport($evaluationReport);
        }
    }

}
