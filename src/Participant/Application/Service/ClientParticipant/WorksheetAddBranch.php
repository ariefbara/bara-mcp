<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\ {
    Firm\Program\MissionRepository,
    Participant\WorksheetRepository
};
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
            string $firmId, string $clientId, string $programParticipationId, string $worksheetId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();

        $mission = $this->missionRepository
                ->aMissionInProgramWhereClientParticipate($firmId, $clientId, $programParticipationId, $missionId);
        
        $worksheet = $this->worksheetRepository
                ->aWorksheetBelongsToClientParticipant($firmId, $clientId, $programParticipationId, $worksheetId)
                ->createBranchWorksheet($id, $name, $mission, $formRecordData);
        
        $this->worksheetRepository->add($worksheet);
        return $id;
    }

}
