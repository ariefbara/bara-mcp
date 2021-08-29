<?php

namespace Query\Domain\Task\InProgram;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;

class ParticipantEvaluationReportTranscriptResult implements IProgramEvaluationReportSummaryResult
{
    
    /**
     * 
     * @var TranscriptTable[]
     */
    protected $transcriptTables;
    
    public function __construct()
    {
        $this->transcriptTables = [];
    }

        public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $includedInExistingTable = false;
        foreach ($this->transcriptTables as $transcriptTable) {
            if ($transcriptTable->canInclude($evaluationReport)) {
                $transcriptTable->includeEvaluationReport($evaluationReport);
                $includedInExistingTable = true;
                break;
            }
        }
        
        if (!$includedInExistingTable) {
            $this->transcriptTables[] = new TranscriptTable($evaluationReport);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        foreach ($this->transcriptTables as $transcriptTable) {
            $transcriptTable->saveToSpreadsheet($spreadsheet);
        }
    }

    public function toRelationalArray(): array
    {
        $result = [];
        foreach ($this->transcriptTables as $transcriptTable) {
            $result[] = $transcriptTable->toRelationalArray();
        }
        return $result;
    }

}
