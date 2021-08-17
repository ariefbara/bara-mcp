<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ParticipantEvaluationReportTranscript
{
    /**
     * 
     * @var EvaluationPlanReportSummary[]
     */
    protected $evaluationPlanReportSummaries;
    
    public function __construct()
    {
        $this->evaluationPlanReportSummaries = [];
    }
    
    public function includeEvaluationReport(EvaluationReport $mentorEvaluationReport): void
    {
        $corresponsdingEvaluationPlanReportSummary = null;
        foreach ($this->evaluationPlanReportSummaries as $evaluationPlanReportSummary) {
            if ($evaluationPlanReportSummary->canInclude($mentorEvaluationReport)) {
                $corresponsdingEvaluationPlanReportSummary = $evaluationPlanReportSummary;
                break;
            }
        }
        if (isset($corresponsdingEvaluationPlanReportSummary)) {
            $corresponsdingEvaluationPlanReportSummary->includeEvaluationReport($mentorEvaluationReport);
        } else {
            $this->evaluationPlanReportSummaries[] = $mentorEvaluationReport->createEvaluationPlanReportSummary();
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $arrayTable = [];
        $firstData = true;
        foreach ($this->evaluationPlanReportSummaries as $evaluationPlanReportSummary) {
            if (!$firstData) {
                $arrayTable[] = [""];
            }
            $arrayTable[] = [$evaluationPlanReportSummary->getEvaluationPlanName()];
            foreach ($evaluationPlanReportSummary->toTrascriptTableArray() as $transcriptEntry) {
                $arrayTable[] = $transcriptEntry;
            }
            $firstData = false;
        }
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($arrayTable);
    }
    
    public function toArray(): array
    {
        $result = [];
        foreach ($this->evaluationPlanReportSummaries as $evaluationPlanReportSummary) {
            $result[] = [
                "evaluationPlan" => [
                    "id" => $evaluationPlanReportSummary->getEvaluationPlanId(),
                    "name" => $evaluationPlanReportSummary->getEvaluationPlanName(),
                ],
                "transcriptTable" => $evaluationPlanReportSummary->toTrascriptTableArray(),
            ];
        }
        return $result;
    }

}
