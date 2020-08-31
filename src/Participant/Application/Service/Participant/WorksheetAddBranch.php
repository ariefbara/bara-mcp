<?php

namespace Participant\Application\Service\Participant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class WorksheetAddBranch
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(WorksheetRepository $worksheetRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();

        $mission = $this->missionRepository->ofId($firmId, $programId, $missionId);
        $formRecord = $mission->createWorksheetFormRecord($id, $formRecordData);
        
        $worksheet = $this->worksheetRepository->aWorksheetOfClientParticipant($firmId, $clientId, $programId, $worksheetId)
                ->createBranchWorksheet($id, $name, $mission, $formRecord);
        
        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
