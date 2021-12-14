<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;

class WriteEvaluationReportToSpreadsheetTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var IReportSpreadsheet
     */
    protected $reportSpreadsheet;

    /**
     * 
     * @var EvaluationReportSummaryFilter
     */
    protected $payload;

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository, IReportSpreadsheet $reportSpreadsheet,
            EvaluationReportSummaryFilter $payload)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->reportSpreadsheet = $reportSpreadsheet;
        $this->payload = $payload;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedActiveEvaluationReportsInFirm($firm, $this->payload);
        foreach ($evaluationReports as $evaluationReport) {
            $this->reportSpreadsheet->insertReport($evaluationReport);
        }
    }

}
