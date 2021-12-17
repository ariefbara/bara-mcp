<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\IReportColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

class FieldNameColumn implements IReportColumn
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
     * @var string
     */
    protected $fieldName;
    
    protected function setFieldName(string $fieldName): void
    {
        \Resources\ValidationService::build()
                ->addRule(\Resources\ValidationRule::notEmpty())
                ->execute($fieldName, 'bad request: field label is mandatory for generating report using field name');
        $this->fieldName = $fieldName;
    }

    public function __construct(ReportSheet $reportSheet, FieldNameColumnPayload $payload)
    {
        $this->reportSheet = $reportSheet;
        $this->fieldName = $payload->getFieldName();
        $this->colNumber = $payload->getColNumber();
        $this->reportSheet->addHeaderColumnLabel($this->colNumber, $this->fieldName);
    }

    public function insertCorrespondingReportValue(EvaluationReport $report): void
    {
        $value = $report->getRecordValueOfFieldWithName($this->fieldName);
        $this->reportSheet->insertIntoCell($this->colNumber, $value);
    }

}
