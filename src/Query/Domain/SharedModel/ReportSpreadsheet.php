<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ReportSpreadsheet
{

    /**
     * 
     * @var IReportSheet[]
     */
    protected $reportSheets = [];

    public function __construct()
    {
    }

    public function addReportSheet(ReportSpreadsheet\IReportSheet $reportSheet): void
    {
        $this->reportSheets[] = $reportSheet;
    }

    public function insertReport(EvaluationReport $report): bool
    {
        foreach ($this->reportSheets as $reportSheeet) {
            if ($reportSheeet->includeReport($report)) {
                return true;
            }
        }
        return false;
    }

}
