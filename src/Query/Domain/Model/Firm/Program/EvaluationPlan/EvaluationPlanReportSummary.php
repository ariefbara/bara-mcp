<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class EvaluationPlanReportSummary
{

    /**
     * 
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     * 
     * @var EvaluationReport[]
     */
    protected $mentorEvaluationReports;

    public function getEvaluationPlan(): EvaluationPlan
    {
        return $this->evaluationPlan;
    }
    public function getEvaluationPlanName(): string
    {
        return $this->evaluationPlan->getName();
    }
    public function getEvaluationPlanId(): string
    {
        return $this->evaluationPlan->getId();
    }

    public function __construct(EvaluationPlan $evaluationPlan, EvaluationReport $firstMentorEvaluationReport)
    {
        $this->evaluationPlan = $evaluationPlan;
        $this->mentorEvaluationReports[] = $firstMentorEvaluationReport;
    }

    public function includeEvaluationReport(EvaluationReport $mentorEvaluationReport): void
    {
        $this->mentorEvaluationReports[] = $mentorEvaluationReport;
    }

    public function canInclude(EvaluationReport $mentorEvaluationReport): bool
    {
        return $mentorEvaluationReport->evaluationPlanEquals($this->evaluationPlan);
    }

    protected function toSummaryTableArray(): array
    {
        $summaryTable = [];
        $summaryTable[] = $this->evaluationPlan->toArrayOfSummaryTableHeader();
        foreach ($this->mentorEvaluationReports as $evaluationReport) {
            $summaryTable[] = $evaluationReport->toArrayOfSummaryTableEntry();
        }
        return $summaryTable;
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $worksheet = $spreadsheet->createSheet();
        $worksheet->setTitle($this->evaluationPlan->getName());
        $worksheet->fromArray($this->toSummaryTableArray());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->evaluationPlan->getId(),
            'name' => $this->evaluationPlan->getName(),
            'summaryTable' => $this->toSummaryTableArray(),
        ];
    }
    
    public function toTrascriptTableArray(): array
    {
        $horizontanTranscriptTable = [];
        $horizontanTranscriptTable[] = $this->evaluationPlan->toArrayOfHorizontalTranscriptTableHeader();
        foreach ($this->mentorEvaluationReports as $evaluationReport) {
            $horizontanTranscriptTable[] = $evaluationReport->toArrayOfHorizontalTranscriptTableEntry();
        }
        
        $transcriptTable = [];
        foreach ($horizontanTranscriptTable as $rowKey => $horizontalEntry) {
            foreach ($horizontalEntry as $columnKey => $columnEntry) {
                $transcriptTable[$columnKey][$rowKey] = $columnEntry;
            }
        }
        return $transcriptTable;
    }

}
