<?php

namespace Query\Domain\Model\Firm\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ClientTranscriptTable;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class TranscriptTable
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var ClientTranscriptTable[]
     */
    protected $clientTranscriptTables;
    
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->clientTranscriptTables = [];
    }

    
    public function canInclude(EvaluationReport $evaluationReport): bool
    {
        return $evaluationReport->correspondWithClient($this->client);
    }
    
    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $includedInExistingTable = false;
        foreach ($this->clientTranscriptTables as $clientTranscriptTable) {
            if ($clientTranscriptTable->canInclude($evaluationReport)) {
                $clientTranscriptTable->includeEvaluationReport($evaluationReport);
                $includedInExistingTable = true;
                break;
            }
        }
        if (!$includedInExistingTable) {
            $this->clientTranscriptTables[] = new ClientTranscriptTable($evaluationReport);
        }
    }
    
    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $worksheet = $spreadsheet->createSheet();
        $worksheet->setTitle(substr($this->client->getFullName(), 0, 30));
        
        $transcripTable = [];
        foreach ($this->clientTranscriptTables as $clientTranscripTable) {
            if (empty($transcripTable)) {
                $transcripTable = [
                    [$clientTranscripTable->getEvaluationPlanName()],
                ];
            } else {
                $transcripTable = array_merge($transcripTable, [
                    [],
                    [$clientTranscripTable->getEvaluationPlanName()],
                ]);
            }
            $transcripTable = array_merge($transcripTable, $clientTranscripTable->toSimplifiedTranscriptFormatArray());
        }
        $worksheet->fromArray($transcripTable);
    }
    
    public function toRelationalArray(): array
    {
        $result = [
            'id' => $this->client->getId(),
            'name' => $this->client->getFullName(),
        ];
        foreach ($this->clientTranscriptTables as $clientTranscriptTable) {
            $result['evaluationPlans'][] = $clientTranscriptTable->toRelationalArray();
        }
        return $result;
    }

}
