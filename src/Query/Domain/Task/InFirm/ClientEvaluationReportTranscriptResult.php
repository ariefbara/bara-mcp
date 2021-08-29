<?php

namespace Query\Domain\Task\InFirm;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Client\TranscriptTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class ClientEvaluationReportTranscriptResult implements IFirmEvaluationReportSummaryResult
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
    
    public function addTranscriptTableForClient(Client $client): void
    {
        $this->transcriptTables[] = new TranscriptTable($client);
    }

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        foreach ($this->transcriptTables as $transcripTable) {
            if ($transcripTable->canInclude($evaluationReport)) {
                $transcripTable->includeEvaluationReport($evaluationReport);
            }
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
