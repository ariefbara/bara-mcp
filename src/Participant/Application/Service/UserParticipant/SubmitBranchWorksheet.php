<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\Participant\WorksheetRepository;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitBranchWorksheet
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
            string $userId, string $userParticipantId, string $worksheetId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();

        $mission = $this->missionRepository
                ->aMissionInProgramWhereUserParticipate($userId, $userParticipantId, $missionId);
        
        $worksheet = $this->worksheetRepository
                ->aWorksheetBelongsToUserParticipant($userId, $userParticipantId, $worksheetId)
                ->createBranchWorksheet($id, $name, $mission, $formRecordData);
        
        $this->worksheetRepository->add($worksheet);
        return $id;
    }
}
