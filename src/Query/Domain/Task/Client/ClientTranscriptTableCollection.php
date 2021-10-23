<?php

namespace Query\Domain\Task\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;

class ClientTranscriptTableCollection
{
    /**
     * 
     * @var TranscriptTable[]
     */
    protected $transcriptTableCollection;
    
    public function __construct()
    {
        $this->transcriptTableCollection = [];
    }
    
    public function include(EvaluationReport $evaluationReport): void
    {
        $includedInExistinTable = false;
        foreach ($this->transcriptTableCollection as $transcripTable) {
            if ($transcripTable->canInclude($evaluationReport)) {
                $transcripTable->includeEvaluationReport($evaluationReport);
                $includedInExistinTable = true;
                break;
            }
        }
        if (!$includedInExistinTable) {
            $this->transcriptTableCollection[] = new TranscriptTable($evaluationReport);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet, bool $summaryStyleView): void
    {
        foreach ($this->transcriptTableCollection as $transcriptTable) {
            $transcriptTable->saveAsProgramSheet($spreadsheet, $summaryStyleView);
        }
    }
    
    public function toRelationalArray(): array
    {
        $result = [];
        foreach ($this->transcriptTableCollection as $transcriptTable) {
            $result[] = $transcriptTable->toRelationalArrayOfProgram();
        }
        return $result;
    }

}
