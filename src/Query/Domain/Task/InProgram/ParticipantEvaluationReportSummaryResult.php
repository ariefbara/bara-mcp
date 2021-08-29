<?php

namespace Query\Domain\Task\InProgram;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ParticipantSummaryTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ParticipantEvaluationReportSummaryResult implements IProgramEvaluationReportSummaryResult
{

    /**
     * 
     * @var ParticipantSummaryTable[]
     */
    protected $participantSummaryTables;

    public function __construct()
    {
        $this->participantSummaryTables = [];
    }

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $includedInExistingTable = false;
        foreach ($this->participantSummaryTables as $participantSummaryTable) {
            if ($participantSummaryTable->canInclude($evaluationReport)) {
                $participantSummaryTable->includeEvaluationReport($evaluationReport);
                $includedInExistingTable = true;
                break;
            }
        }
        if (!$includedInExistingTable) {
            $this->participantSummaryTables[] = new ParticipantSummaryTable($evaluationReport);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        foreach ($this->participantSummaryTables as $participantSummaryTable) {
            $participantSummaryTable->saveToSpreadsheet($spreadsheet);
        }
    }

    public function toRelationalArray(): array
    {
        $result = [];
        foreach ($this->participantSummaryTables as $participantSummaryTable) {
            $result[] = $participantSummaryTable->toRelationalArray();
        }
        return $result;
    }

}
