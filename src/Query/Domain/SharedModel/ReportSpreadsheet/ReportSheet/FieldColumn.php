<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Shared\Form\IntegerField;
use Query\Domain\SharedModel\ReportSpreadsheet\IReportColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

class FieldColumn implements IReportColumn
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
    
    /**
     * 
     * @var IField
     */
    protected $field;

    public function __construct(ReportSheet $reportSheet, int $colNumber, IField $field)
    {
        $this->reportSheet = $reportSheet;
        $this->colNumber = $colNumber;
        $this->field = $field;
        $this->reportSheet->addHeaderColumnLabel($this->colNumber, $this->field->getLabel());
    }

    public function insertCorrespondingReportValue(EvaluationReport $report): void
    {
        $value = $this->field->getCorrespondingValueFromEvaluationReport($report);
        $this->reportSheet->insertIntoCell($this->colNumber, $value);
    }

}
