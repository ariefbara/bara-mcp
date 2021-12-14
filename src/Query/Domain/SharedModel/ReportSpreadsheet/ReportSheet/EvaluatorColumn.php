<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\IReportColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

class EvaluatorColumn implements IReportColumn
{

    /**
     * 
     * @var ReportSheet
     */
    protected $reportSheet;

    /**
     * 
     * @var int
     */
    protected $colNumber;

    public function __construct(ReportSheet $reportSheet, int $colNumber)
    {
        $this->reportSheet = $reportSheet;
        $this->colNumber = $colNumber;
        $this->reportSheet->addHeaderColumnLabel($this->colNumber, 'Mentor');
    }

    public function insertCorrespondingReportValue(EvaluationReport $report): void
    {
        $value = $report->getMentorName();
        $this->reportSheet->insertIntoCell($this->colNumber, $value);
    }

}
