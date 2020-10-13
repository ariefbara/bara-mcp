<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\{
    Participant\WorksheetRepository,
    UserParticipantRepository
};
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
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(WorksheetRepository $worksheetRepository,
            UserParticipantRepository $userParticipantRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $userId, string $userParticipantId, string $worksheetId, string $missionId, string $name,
            FormRecordData $formRecordData): string
    {
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository->ofId($missionId);
        $parentWorksheet = $this->worksheetRepository->ofId($worksheetId);
        
        $branch = $this->userParticipantRepository->ofId($userId, $userParticipantId)
                ->submitBranchWorksheet($parentWorksheet, $id, $name, $mission, $formRecordData);

        $this->worksheetRepository->add($branch);
        return $id;
    }

}
