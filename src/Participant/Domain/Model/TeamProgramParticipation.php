<?php

namespace Participant\Domain\Model;

use Participant\Domain\{
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class TeamProgramParticipation
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $programParticipation;

    protected function __construct()
    {
        
    }

    public function teamEquals(Team $team): bool
    {
        return $this->team === $team;
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->programParticipation->isActiveParticipantOfProgram($program);
    }

    public function submitRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->programParticipation->createRootWorksheet($worksheetId, $name, $mission, $formRecordData);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        return $this->programParticipation
                        ->submitBranchWorksheet($parentWorksheet, $worksheetId, $name, $mission, $formRecordData);
    }
    
    public function updateWorksheet(Worksheet $worksheet, string $name, FormRecordData $formRecordData): void
    {
        $this->programParticipation->updateWorksheet($worksheet, $name, $formRecordData);
    }
    
    public function quit(): void
    {
        $this->programParticipation->quit();
    }
    

}
