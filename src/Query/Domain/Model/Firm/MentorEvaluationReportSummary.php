<?php

namespace Query\Domain\Model\Firm;

class MentorEvaluationReportSummary
{
    protected $spreadsheet;
    /**
     * 
     * @var EvaluationPlanSheet[]
     */
    protected $evaluationPlanSheets;
    
    public function __construct()
    {
        ;
    }
    
    public function addReport(Program\Participant\DedicatedMentor\EvaluationReport $evaluationReport): void
    {
        
    }
}
