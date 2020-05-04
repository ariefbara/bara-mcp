<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\Program\Mission
};

class MissionAddBranch
{

    protected $missionRepository;
    protected $worksheetFormRepository;

    function __construct(MissionRepository $missionRepository, WorksheetFormRepository $worksheetFormRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    public function execute(
        ProgramCompositionId $programCompositionId, string $missionId, string $name, ?string $description, 
        string $worksheetFormId, ?string $position): string
    {
        $id = $this->missionRepository->nextIdentity();
        $worksheetForm = $this->worksheetFormRepository->ofId($programCompositionId->getFirmId(), $worksheetFormId);
        
        $mission = $this->missionRepository->ofId($programCompositionId, $missionId)
            ->createBranch($id, $name, $description, $worksheetForm, $position);
        
        $this->missionRepository->add($mission);
        return $id;
    }

}
