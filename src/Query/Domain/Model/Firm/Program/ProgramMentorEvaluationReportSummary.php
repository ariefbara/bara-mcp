<?php

namespace Query\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ProgramMentorEvaluationReportSummary
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
        $correspondingEvaluationPlanReportSummary = null;
        foreach ($this->evaluationPlanReportSummaries as $evaluationPlanReportSummary) {
            if ($evaluationPlanReportSummary->canInclude($mentorEvaluationReport)) {
                $correspondingEvaluationPlanReportSummary = $evaluationPlanReportSummary;
                break;;
            }
        }
        
        if (!empty($correspondingEvaluationPlanReportSummary)) {
            $correspondingEvaluationPlanReportSummary->includeEvaluationReport($mentorEvaluationReport);
        } else {
            $this->evaluationPlanReportSummaries[] = $mentorEvaluationReport->createEvaluationPlanReportSummary();
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        foreach ($this->evaluationPlanReportSummaries as $evaluationReportPlanSummary) {
            $evaluationReportPlanSummary->saveToSpreadsheet($spreadsheet);
        }
    }
    
    public function toArray(): array
    {
        $result = [];
        foreach ($this->evaluationPlanReportSummaries as $evaluationPlanReportSummary) {
            $result[] = $evaluationPlanReportSummary->toArray();
        }
        return $result;
    }

}
