<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ReportWorkbook
{
    /**
     * 
     * @var IReportSpreadsheet[]
     */
    protected $reportSpreadsheets = [];
    
    public function __construct()
    {
        
    }
    
    public function addReportSpreadsheet(IReportSpreadsheet $reportSpreadsheet): void
    {
        $this->reportSpreadsheets[] = $reportSpreadsheet;
    }

    
    public function insertReport(EvaluationReport $report): void
    {
        foreach ($this->reportSpreadsheets as $reportSpreadsheet) {
            $reportSpreadsheet->insertReport($report);
        }
    }
}
