<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\EvaluateeColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\EvaluationColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\EvaluatorColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\FieldColumn;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\SubmitTimeColumn;

class ReportSheet implements ISheetContainer
{

    /**
     * 
     * @var IReportColumn
     */
    protected $reportColumns;

    /**
     * 
     * @var ISheet
     */
    protected $sheet;

    /**
     * 
     * @var int
     */
    protected $nextEntryRowNumber = 2;

    protected function setEvaluationColumn(ReportSheetPayload $payload): void
    {
        if ($payload->isEvaluationInspected()) {
            $this->reportColumns[] = new EvaluationColumn($this, $payload->getEvaluationColNumber());
        }
    }

    protected function setEvaluatorColumn(ReportSheetPayload $payload): void
    {
        if ($payload->isEvaluatorInspected()) {
            $this->reportColumns[] = new EvaluatorColumn($this, $payload->getEvaluatorColNumber());
        }
    }

    protected function setEvaluateeColumn(ReportSheetPayload $payload): void
    {
        if ($payload->isEvaluateeInspected()) {
            $this->reportColumns[] = new EvaluateeColumn($this, $payload->getEvaluateeColNumber());
        }
    }

    protected function setSubmitTimeColumn(ReportSheetPayload $payload): void
    {
        if ($payload->isSubmitTimeInspected()) {
            $this->reportColumns[] = new SubmitTimeColumn($this, $payload->getSubmitTimeColNumber());
        }
    }
    
    public function getColumnCount(): int
    {
        return count($this->reportColumns);
    }

    public function __construct(ReportSheetPayload $payload, ISheet $sheet)
    {
        $this->reportColumns = [];
        $this->sheet = $sheet;
        $this->setEvaluationColumn($payload);
        $this->setEvaluatorColumn($payload);
        $this->setEvaluateeColumn($payload);
        $this->setSubmitTimeColumn($payload);
    }

    public function addHeaderColumnLabel(int $colNumber, string $label): void
    {
        $this->sheet->insertIntoCell(1, $colNumber, $label);
    }

    public function addFieldColumn(ReportSheet\IField $field, ?int $colNumber): void
    {
        if (is_null($colNumber)) {
            $colNumber = count($this->reportColumns) + 1;
        }
        $this->reportColumns[] = new FieldColumn($this, $colNumber, $field);
    }

    public function includeReport(EvaluationReport $report): void
    {
        foreach ($this->reportColumns as $reportColumn) {
            $reportColumn->insertCorrespondingReportValue($report);
        }
        $this->nextEntryRowNumber++;
    }

    public function insertIntoCell(int $colNumber, $value): void
    {
        $this->sheet->insertIntoCell($this->nextEntryRowNumber, $colNumber, $value);
    }

    public function setLabel(string $label): void
    {
        $this->sheet->setLabel($label);
    }

}
