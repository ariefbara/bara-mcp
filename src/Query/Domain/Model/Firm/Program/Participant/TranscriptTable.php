<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\Program\EvaluationPlan\ParticipantTranscriptTable;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

class TranscriptTable
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var ParticipantTranscriptTable[]
     */
    protected $participantTranscriptTables;
    
    public function __construct(EvaluationReport $evaluationReport)
    {
        $this->participantTranscriptTables = [];
        $this->participant = $evaluationReport->getParticipant();
        $this->participantTranscriptTables[] = new ParticipantTranscriptTable($evaluationReport);
    }
    
    public function canInclude(EvaluationReport $evaluationReport): bool
    {
        return $evaluationReport->correspondWithParticipant($this->participant);
    }

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void
    {
        $includedInExistingTable = false;
        foreach ($this->participantTranscriptTables as $participantTranscriptTable) {
            if ($participantTranscriptTable->canInclude($evaluationReport)) {
                $participantTranscriptTable->includeEvaluationReport($evaluationReport);
                $includedInExistingTable = true;
                break;
            }
        }
        if (!$includedInExistingTable) {
            $this->participantTranscriptTables[] = new ParticipantTranscriptTable($evaluationReport);
        }
    }

    public function saveToSpreadsheet(Spreadsheet $spreadsheet): void
    {
        $worksheet = $spreadsheet->createSheet();
        $sheetTitle = preg_replace("/[^A-Za-z0-9 _-]/", '', $this->participant->getName());
        $worksheet->setTitle(substr($sheetTitle, 0, 30));
        
        $transcripTable = [];
        foreach ($this->participantTranscriptTables as $participantTranscripTable) {
            if (empty($transcripTable)) {
                $transcripTable = [
                    [$participantTranscripTable->getEvaluationPlanName()],
                ];
            } else {
                $transcripTable = array_merge($transcripTable, [
                    [],
                    [$participantTranscripTable->getEvaluationPlanName()],
                ]);
            }
            $transcripTable = array_merge($transcripTable, $participantTranscripTable->toSimplifiedTranscriptFormatArray());
        }
        
        $worksheet->fromArray($transcripTable);
    }

    public function toRelationalArray(): array
    {
        $result = [
            'id' => $this->participant->getId(),
            'name' => $this->participant->getName(),
        ];
        foreach ($this->participantTranscriptTables as $participantTranscriptTable) {
            $result['evaluationPlans'][] = $participantTranscriptTable->toRelationalArray();
        }
        return $result;
    }

}
